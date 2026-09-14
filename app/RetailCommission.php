<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailCommission extends Model
{
    protected $table = "tbl_retail_commission_list";
    protected $guarded = [];


    public function list()
    {
        return $this->hasMany(RetailCommissionList::class, 'foreign_id', 'id');
    }
    public function campaign()
    {
        return $this->hasOne(RetailCommissionCampaign::class, 'foreign_id', 'id');
    }
    public function showroom()
    {
        return $this->hasOne(ShowroomSetup::class, 'id', 'showroom_id');
    }
}
