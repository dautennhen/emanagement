<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VNpayInvoice extends Model
{
    protected $table = 'vnpay_invoices';
    protected $dates = [
        'pay_date',
        'next_pay_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
