<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupSetup extends Model {

    protected $table = "tbl_groups";
    protected $fillable = [
        'company_id', 'showroom_id', 'name', 'team_leader', 'team_member', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
