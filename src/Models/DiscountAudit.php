<?php


namespace HipstersgDemo\LaravelUserDiscountsPackage\Models;


use Illuminate\Database\Eloquent\Model;


class DiscountAudit extends Model
{
    protected $table = 'discount_audits';


    protected $fillable = [
        'user_id',
        'discount_id',
        'amount_before',
        'amount_after',
        'idempotency_key',
        'meta',
        'success'
    ];


    protected $casts = [
        'meta' => 'array',
        'success' => 'boolean',
    ];
}
