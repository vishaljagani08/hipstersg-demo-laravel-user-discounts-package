<?php
namespace HipstersgDemo\LaravelUserDiscountsPackage\Tests\Unit;

use HipstersgDemo\LaravelUserDiscountsPackage\Tests\TestCase; // 👈 extend package TestCase
use HipstersgDemo\LaravelUserDiscountsPackage\Models\Discount;
use HipstersgDemo\LaravelUserDiscountsPackage\Models\UserDiscount;
use HipstersgDemo\LaravelUserDiscountsPackage\Services\DiscountManager;
use Illuminate\Support\Facades\Event;
use App\Models\User; // comes from Laravel default migrations
use HipstersgDemo\LaravelUserDiscountsPackage\Events\DiscountApplied;

class DiscountManagerTest extends TestCase
{
    /** @test */
    public function it_applies_a_discount_and_respects_per_user_cap()
    {
        Event::fake();

        $user = User::factory()->create();

        $discount = Discount::create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'per_user_cap' => 1,
        ]);

        $this->app->make(DiscountManager::class)->assign($user, $discount);

        $result1 = $this->app->make(DiscountManager::class)->apply($user, 100.00);
        $this->assertEquals(90.00, $result1);

        $result2 = $this->app->make(DiscountManager::class)->apply($user, 100.00);
        $this->assertEquals(100.00, $result2);

        $this->assertEquals(1, UserDiscount::first()->usage_count);

        Event::assertDispatchedTimes(DiscountApplied::class, 1);
    }
}
