<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingRefInvoice extends Model
{
    protected $table = "ref_invoices";

    protected $fillable = [
        'invoice_id',
        'manager_id',
        'user_id',
        'amount',
        'type_id',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function details()
    {
        return $this->hasMany('App\BillingRefInvoiceDetail', 'ref_invoice_id', 'id');
    }
}
