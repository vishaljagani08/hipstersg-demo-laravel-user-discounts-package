<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage\Services;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Event;
use HipstersgDemo\LaravelUserDiscountsPackage\Models\{Discount, UserDiscount, DiscountAudit};
use HipstersgDemo\LaravelUserDiscountsPackage\Events\{DiscountAssigned, DiscountRevoked, DiscountApplied};
use Carbon\Carbon;

class DiscountManager
{
    public function __construct(protected ConnectionInterface $db) {}

    /**
     * Assign a discount to a user.
     */
    public function assign($user, Discount $discount, array $meta = []): UserDiscount
    {
        $pivot = $discount->users()->syncWithoutDetaching([
            $user->id => [
                'assigned_at' => now(),
                'revoked_at' => null,
                'usage_count' => 0,
            ]
        ]);

        Event::dispatch(new DiscountAssigned($user, $discount, $meta));
        return $discount->users()->where('user_id', $user->id)->first()->pivot;
    }

    /**
     * Revoke a discount from a user.
     */
    public function revoke($user, Discount $discount): void
    {
        $discount->users()->updateExistingPivot($user->id, [
            'revoked_at' => now(),
        ]);

        Event::dispatch(new DiscountRevoked($user, $discount));
    }

    /**
     * Check if a user is eligible for a discount.
     */
    public function eligibleFor($user, Discount $discount): bool
    {
        if (!$discount->active) return false;
        if ($discount->starts_at && Carbon::now()->lt($discount->starts_at)) return false;
        if ($discount->ends_at && Carbon::now()->gt($discount->ends_at)) return false;

        $pivot = $discount->users()->where('user_id', $user->id)->first()?->pivot;
        if (!$pivot || $pivot->revoked_at) return false;

        if ($discount->per_user_cap > 0 && $pivot->usage_count >= $discount->per_user_cap) {
            return false;
        }

        if ($discount->global_cap > 0 && $discount->global_usage >= $discount->global_cap) {
            return false;
        }

        return true;
    }

    /**
     * Apply discounts to a user’s amount.
     */
    public function apply($user, float $amount, array $options = []): array
    {
        $idempotencyKey = $options['idempotency_key'] ?? null;

        return $this->db->transaction(function () use ($user, $amount, $idempotencyKey, $options) {
            // Check for existing audit if idempotency
            if ($idempotencyKey) {
                $existing = DiscountAudit::where('user_id', $user->id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();
                if ($existing) {
                    return [
                        'amount' => $existing->amount_after,
                        'audit' => $existing,
                        'reused' => true,
                    ];
                }
            }

            $discounts = $user->discounts()
                ->wherePivotNull('revoked_at')
                ->where('active', true)
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->orderBy('stacking_priority', 'desc')
                ->get();

            $originalAmount = $amount;
            foreach ($discounts as $discount) {
                if (!$this->eligibleFor($user, $discount)) continue;

                $before = $amount;

                if ($discount->type === 'percentage') {
                    $amount -= ($amount * ($discount->value / 100));
                } elseif ($discount->type === 'fixed') {
                    $amount -= $discount->value;
                }

                // Rounding
                $precision = config('discounts.rounding.precision', 2);
                $mode = config('discounts.rounding.mode', 'half_up');
                $amount = $this->round($amount, $precision, $mode);

                // Increment usage
                $pivot = $discount->users()->where('user_id', $user->id)->lockForUpdate()->first()->pivot;
                $pivot->usage_count++;
                $pivot->save();

                $discount->increment('global_usage');
            }

            $audit = DiscountAudit::create([
                'user_id' => $user->id,
                'discount_id' => $discounts->first()->id ?? null,
                'amount_before' => $originalAmount,
                'amount_after' => $amount,
                'idempotency_key' => $idempotencyKey,
                'meta' => $options['meta'] ?? [],
                'success' => true,
            ]);

            Event::dispatch(new DiscountApplied($user, $discounts, $audit));

            return [
                'amount' => $amount,
                'audit' => $audit,
                'reused' => false,
            ];
        });
    }

    protected function round(float $value, int $precision, string $mode): float
    {
        $factor = pow(10, $precision);
        return match ($mode) {
            'up' => ceil($value * $factor) / $factor,
            'down' => floor($value * $factor) / $factor,
            'half_down' => round($value * $factor, 0, PHP_ROUND_HALF_DOWN) / $factor,
            default => round($value * $factor, 0, PHP_ROUND_HALF_UP) / $factor,
        };
    }
}
