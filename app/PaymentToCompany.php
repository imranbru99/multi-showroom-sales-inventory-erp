<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentToCompany extends Model {

    protected $table = "tbl_payment_to_company";
    protected $fillable = [
        'company_id', 'showroom_id', 'vendor_id', 'payment_no', 'payment_date', 'current_due', 'payment_now', 'balance', 'money_receipt', 'payment_type', 'remarks', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
