<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RootPlanProducts extends Model
{
    protected $table = "tbl_root_plan_products";
    protected $guarded = [];


    public function root_plan()
    {
        return $this->hasOne(RootPlan::class, 'id', 'root_plan_id');
    }
    public function dealer()
    {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }
}
