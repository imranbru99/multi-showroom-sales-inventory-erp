<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShowroomSetup extends Model {

    protected $table = "tbl_showroom";
    protected $fillable = [
        'company_id', 'prefix', 'name', 'contact_person', 'email', 'phone', 'fax', 'website', 'vat', 'tin', 'trade_license', 'address', 'status'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function company() {
        return $this->hasOne(CompanySetup::class, 'id', 'company_id');
    }

}
