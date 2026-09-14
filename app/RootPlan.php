<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RootPlan extends Model
{
    protected $table = "tbl_root_plans";
    protected $guarded = [];


    public function sale()
    {
        return $this->hasOne(RetailSale::class, 'id', 'sale_id');
    }
    public function vehicle()
    {
        return $this->hasOne(VehicleSetup::class, 'id', 'vehicle_id');
    }
    public function products()
    {
        return $this->hasMany(RootPlanProducts::class, 'root_plan_id', 'id');
    }
}
