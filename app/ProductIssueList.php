<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductIssueList extends Model {

    protected $table = "tbl_product_issue_lists";
    protected $fillable = [
        'company_id', 'collection_id', 'collection', 'showroom_id', 'issue_id', 'product_id', 'model_no', 'serial_no', 'commission_rate', 'offer', 'price', 'qty', 'amount', 'status', 'created_by', 'updated_by', 'isReturn'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Get the issue associated with the ProductIssueList
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function issue() {
        return $this->hasOne(ProductIssue::class, 'id', 'issue_id');
    }

    /**
     * Get the product associated with the ProductIssueList
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function lifting() {
        return $this->hasOne(LiftingProduct::class, 'serial_no', 'serial_no');
    }

}
