<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = "tbl_products";
    protected $fillable = [
        'company_id', 'showroom_id', 'old', 'category_id', 'name', 'code', 'productSize', 'model_no', 'color', 'uom', 'price', 'mrp_price', 'haire_price', 'discount', 'warranty', 'reorder_level_qty', 'order_by', 'transport_point', 'status', 'youtube_link', 'tag_line', 'short_description', 'long_description', 'meta_title', 'meta_keyword', 'meta_description', 'power', 'powerConsumption', 'pdtType_status', 'product_type'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Get the category associated with the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category() {
        return $this->hasOne(CategorySetup::class, 'id', 'category_id');
    }

}
