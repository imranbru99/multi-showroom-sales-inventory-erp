<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorSetup extends Model {

    protected $table = "tbl_vendors";
    protected $fillable = [
        'company_id', 'code', 'name', 'contact_person', 'contact', 'email', 'address', 'status', 'showroom_id'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
