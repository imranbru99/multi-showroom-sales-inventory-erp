<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceReport extends Model
{
    protected $table = "tbl_previous_invoices";

    protected $fillable = [
		'date','vendor_name','challan_no','invoice_no','product_name','model_no','qty','mrp','cost_price','discount_amount','special_discount_amount','invoice_amount','total_amount','remarks'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
