<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerCommissionList extends Model
{

    protected $table = "tbl_dealer_commission_list";
    protected $fillable = [
        'dealer_commission_id', 'category_id', 'category_name', 'commission',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function commission()
    {
        return $this->hasOne(DealerCommission::class, 'id', 'dealer_commission_id');
    }
}
