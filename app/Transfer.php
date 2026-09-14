<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model {

    protected $table = "tbl_transfers";
    protected $fillable = [
        'company_id', 'showroom_id', 'vendor_id', 'product_type', 'transfer_no', 'date', 'host_type', 'host_id', 'destination_type', 'destination_id', 'product_id', 'total_qty', 'status', 'approve_by', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    
    public function host() {
        return $this->hasOne(StoreSetup::class, 'id', 'host_id');
    }
    
    public function destination() {
        return $this->hasOne(StoreSetup::class, 'id', 'destination_id');
    }
    
    public function approve() {
        return $this->hasOne(Admin::class, 'id', 'approve_by');
    }
         
    public function sendBy() {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }

}
