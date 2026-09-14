<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerProduct extends Model
{
    protected $table = "tbl_customer_products";

    protected $fillable = [
		'customer_id','product_id','reference_id','product_model','cash_price','mrp_price','showroom_id','qty','warranty','purchase_date','remarks','purchase_type','installment_type','deposite','installment_price','total_installment','monthly_installment_amount','product_usage_address','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];
}
