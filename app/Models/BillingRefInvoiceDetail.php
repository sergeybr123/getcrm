<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingRefInvoiceDetail extends Model
{
    protected $table = "ref_invoice_details";

    protected $fillable = [
        'ref_invoice_id',
        'type',
        'paid_id',
        'paid_type',
        'details',
        'price',
        'quantity',
        'discount',
        'amount',
    ];

    protected $casts = [
        'details' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
