<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage\Events;

use Illuminate\Queue\SerializesModels;
use HipstersgDemo\LaravelUserDiscountsPackage\Models\Discount;

class DiscountRevoked
{
    use SerializesModels;

    public function __construct(public $user, public Discount $discount) {}
}