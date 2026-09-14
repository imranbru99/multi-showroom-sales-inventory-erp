<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignCommission extends Model
{
    protected $table = "tbl_campaign_commissions";
    protected $guarded = [];



        
    public function staff() {
        return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
    }
}
