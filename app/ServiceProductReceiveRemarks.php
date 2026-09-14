<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceProductReceiveRemarks extends Model {

    protected $table = "tbl_service_product_receive_remarks";
    protected $fillable = [
        'showroom_id', 'invoice_no', 'service_receive_id', 'job_allocation_id', 'invoice_no', 'remarks', 'created_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
