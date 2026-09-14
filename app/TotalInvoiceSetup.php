<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TotalInvoiceSetup extends Model
{
    protected $table = "tbl_invoice_total";

    protected $fillable = [
        'showroom_id','invoice_date','invoice_no','total_customer_product_price','status','created_by','updated_by'

    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
