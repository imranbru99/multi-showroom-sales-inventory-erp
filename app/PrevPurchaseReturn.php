<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrevPurchaseReturn extends Model
{
    protected $table = "tbl_previous_purchase_return";
    protected $guarded = [];

    public $timestamps = false;
}
