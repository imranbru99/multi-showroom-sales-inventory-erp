<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiftingProduct extends Model {

    protected $table = "tbl_lifting_products";
    protected $fillable = [
        'company_id', 'showroom_id', 'lifting_id', 'vendor_id', 'product_id', 'product_name', 'store_or_showroom_type', 'store_or_showroom_id', 'serial_no', 'color', 'qty', 'price', 'amount', 'mrp_price', 'haire_price', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Get the lifting associated with the LiftingProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lifting() {
        return $this->hasOne(Lifting::class, 'id', 'lifting_id');
    }
    
    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
