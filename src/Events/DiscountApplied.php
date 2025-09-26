<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage\Events;

use Illuminate\Queue\SerializesModels;
use HipstersgDemo\LaravelUserDiscountsPackage\Models\DiscountAudit;
use Illuminate\Support\Collection;

class DiscountApplied
{
    use SerializesModels;

    public function __construct(public $user, public Collection $discounts, public DiscountAudit $audit) {}
}