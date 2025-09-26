<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage\Events;

use Illuminate\Queue\SerializesModels;
use HipstersgDemo\LaravelUserDiscountsPackage\Models\Discount;

class DiscountAssigned
{
    use SerializesModels;

    public function __construct(public $user, public Discount $discount, public array $meta = []) {}
}
