<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExcelTransfer extends Model
{
    protected $table = "excel_transfers";
    protected $fillable = [
    	'product_id', 'model_no', 'date'
    ];
}
