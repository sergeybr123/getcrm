<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingInvoiceType extends Model
{
    protected $table = "type_invoices";

    protected $fillable = [
        'name',
    ];
}
