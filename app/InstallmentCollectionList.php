<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstallmentCollectionList extends Model {

    protected $table = "tbl_installment_collection_list";
    protected $fillable = [
        'company_id', 'showroom_id', 'project_id', 'installment_id', 'installment_schedule_id', 'installment_collection_id', 'invoice_no', 'installment_schedule_date', 'installment_collection_date', 'installment_schedule_amount', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function installment() {
        return $this->hasOne(Installment::class, 'id', 'installment_id');
    }

    public function collection() {
        return $this->hasOne(InstallmentCollection::class, 'id', 'installment_collection_id');
    }

    public function showroom() {
        return $this->hasOne(ShowroomSetup::class, 'id', 'showroom_id');
    }
    
    public function user() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }

}
