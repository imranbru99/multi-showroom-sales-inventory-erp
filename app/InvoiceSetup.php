<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetup extends Model
{
    protected $table = "tbl_invoice";

    protected $fillable = [
        'invoice_total_id','showroom_id','invoice_date','invoice_no','collection_type','customer_id','retail_sales_id','product_id','product_serial_no','product_name','qty','customer_product_price','customer_product_model','customer_product_color','customer_product_waranty','customer_product_usage_address','customer_product_purchase_date','status','created_by','updated_by'

    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
