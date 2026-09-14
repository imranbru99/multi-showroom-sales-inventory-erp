<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerCommission extends Model {

    protected $table = "tbl_dealer_commission";
    protected $fillable = [
        'company_id', 'showroom_id', 'dealer_id', 'status', 'created_by',
    ];
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function commissionList() {
        return $this->hasMany(DealerCommissionList::class, 'dealer_commission_id', 'id');
    }

    public function dealer() {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }

}
