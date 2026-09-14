<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailCommissionCampaign extends Model
{
    protected $table = "tbl_retail_commission_cmpaigns";
    protected $guarded = [];


    public function list()
    {
        return $this->hasMany(RetailCommissionCampaignList::class, 'foreign_id', 'id');
    }
}
