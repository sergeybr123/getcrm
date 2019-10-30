<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingInvoice extends Model
{
    protected $table = "invoices";

    protected $fillable = [
        'manager_id',
        'user_id',
        'amount',
        'type_id',
        'plan_id',
        'period',
        'service_id',
        'description',
        'paid',
        'status',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function type()
    {
        return $this->hasOne('App\Models\BillingInvoiceType', 'id', 'type_id');
    }

    public function plan()
    {
        return $this->hasOne('App\Models\BillingPlan', 'id', 'plan_id');
    }

    public function ref_invoice()
    {
        return $this->hasOne('App\Models\BillingRefInvoice', 'invoice_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
