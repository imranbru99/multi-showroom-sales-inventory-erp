<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvanceCollection extends Model {

    protected $table = "dealer_advance_collection";
    protected $guarded = [];

    public function dealer() {
        return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
    }

    public function employee() {
        return $this->hasOne(StaffSetup::class, 'id', 'sale_by');
    }

    public function user() {
        return $this->hasOne(Admin::class, 'id', 'added_by');
    }

}
