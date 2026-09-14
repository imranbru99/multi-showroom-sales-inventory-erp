<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreviousRetailCollection extends Model
{
    public $timestamps = false;

    protected $table = "tbl_previous_retail_collections";

    protected $guarded = [];
}
