<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiftingReturnProduct extends Model
{
    protected $table = "tbl_lifting_return_products";

    protected $fillable = [
        'lifting_return_id','lifting_id','lifting_product_id','vendor_id','store_or_showroom_type','store_or_showroom_id','product_id','product_name','model_no','serial_no','color','qty','price', 'amount', 'mrp_price','haire_price','status',
    ];

    public function LiftingReturn()
    {
        return $this->hasOne(LiftingReturn::class, 'id', 'lifting_return_id');
    }

    // public function product()
    // {
    //     return $this->hasOne(Product::class, 'id', 'product_id');
    // }

	protected $hidden = [
		'created_at','updated_at'
	];
}
