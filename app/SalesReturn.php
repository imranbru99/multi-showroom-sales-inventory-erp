<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model {

    protected $table = "tbl_sales_return";
    protected $fillable = [
        'company_id', 'issue_id', 'showroom_id', 'requisition_id', 'dealer_id', 'sales_by', 'product_type', 'issue_type', 'issue_no', 'product_id', 'model_no', 'serial_no', 'commission_rate', 'offer', 'price', 'qty', 'amount', 'return_date', 'reason'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function dealer() {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
