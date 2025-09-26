<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \HipstersgDemo\LaravelUserDiscountsPackage\Models\UserDiscount assign($user, \HipstersgDemo\LaravelUserDiscountsPackage\Models\Discount $discount, array $meta = [])
 * @method static void revoke($user, \HipstersgDemo\LaravelUserDiscountsPackage\Models\Discount $discount)
 * @method static bool eligibleFor($user, \HipstersgDemo\LaravelUserDiscountsPackage\Models\Discount $discount)
 * @method static array apply($user, float $amount, array $options = [])
 */
class Discounts extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'discounts';
    }
}
