<?php


namespace HipstersgDemo\LaravelUserDiscountsPackage\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;


class UserDiscount extends Pivot
{
    protected $table = 'user_discounts';


    protected $fillable = [
        'user_id',
        'discount_id',
        'assigned_at',
        'revoked_at',
        'usage_count'
    ];


    protected $casts = [
        'assigned_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}
