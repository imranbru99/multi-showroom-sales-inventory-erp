<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerDocument extends Model {

    protected $table = "tbl_dealer_documents";
    protected $fillable = [
        'dealer_id', 'title', 'document',
    ];

}
