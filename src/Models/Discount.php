<?php


namespace HipstersgDemo\LaravelUserDiscountsPackage\Models;


use Illuminate\Database\Eloquent\Model;


class Discount extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'active',
        'starts_at',
        'ends_at',
        'stacking_priority',
        'per_user_cap',
        'global_cap',
        'global_usage'
    ];


    protected $casts = [
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];


    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'user_discounts')
            ->withPivot(['assigned_at', 'revoked_at', 'usage_count'])
            ->withTimestamps();
    }
}
