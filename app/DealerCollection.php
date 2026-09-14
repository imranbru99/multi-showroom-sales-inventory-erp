<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerCollection extends Model {

    protected $table = "tbl_dealer_collections";
    protected $fillable = [
        'company_id', 'sale_by', 'advance_id', 'adjustment', 'note', 'bank', 'showroom_id', 'product_issue_id', 'dealer_id', 'payment_no', 'payment_date', 'money_receipt_no', 'money_receipt_type', 'payment_amount', 'remarks', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Get the dealer associated with the DealerCollection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dealer() {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }

    /**
     * Get the advance associated with the DealerCollection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function advance() {
        return $this->hasOne(AdvanceCollection::class, 'id', 'advance_id');
    }

    public function issueList() {
        return $this->hasMany(ProductIssueList::class, 'collection_id', 'id');
    }

    public function employee() {
        return $this->hasOne(StaffSetup::class, 'id', 'sale_by');
    }

    public function user() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }

}
