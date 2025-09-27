<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage\Traits;

use HipstersgDemo\LaravelUserDiscountsPackage\Models\Discount;
use HipstersgDemo\LaravelUserDiscountsPackage\Models\UserDiscount;

trait HasDiscounts
{
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'user_discounts')
            ->using(UserDiscount::class)
            ->withPivot(['usage_count'])
            ->withTimestamps();
    }
}
