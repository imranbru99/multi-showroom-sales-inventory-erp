<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductIssue extends Model {

    protected $table = "tbl_product_issue";
    protected $fillable = [
        'company_id', 'showroom_id', 'product_type', 'requisition_id', 'dealer_id', 'sales_by', 'issue_type', 'issue_no', 'date', 'type', 'total_qty', 'total_amount', 'status', 'created_by', 'updated_by', 'isReturn'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function products() {
        return $this->hasMany(ProductIssueList::class, 'issue_id', 'id');
    }

    public function collection() {
        return $this->hasMany(DealerCollection::class, 'product_issue_id', 'id');
    }

    public function SalesBy() {
        return $this->hasOne(StaffSetup::class, 'id', 'sales_by');
    }

    public function dealer() {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }

    public function showroom() {
        return $this->hasOne(ShowroomSetup::class, 'id', 'showroom_id');
    }

    public function user() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }

}
