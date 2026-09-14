<?php

namespace App;

use App\DealerSetup as AppDealerSetup;
use Illuminate\Database\Eloquent\Model;

class DealerTransfer extends Model
{
    protected $table = "tbl_dealer_transfer";

    protected $fillable = [
        'showroom_id', 'dealer_id', 'transfer_from', 'transfer_to', 'transfer_date', 'created_by',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];



    public function dealer()
    {
        return $this->hasOne(AppDealerSetup::class, 'id', 'dealer_id');
    }

    public function transfer_from()
    {
        return $this->hasOne(StaffSetup::class, 'id', 'transfer_from');
    }

    public function transfer_to()
    {
        return $this->hasOne(StaffSetup::class, 'id', 'transfer_to');
    }
    public function user()
    {
        return $this->hasOne(Admin::class, 'id', 'created_by');
    }
}
