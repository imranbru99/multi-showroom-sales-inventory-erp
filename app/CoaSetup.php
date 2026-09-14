<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoaSetup extends Model {

    protected $table = "tbl_coa";
    protected $fillable = [
        'company_id', 'head_code', 'head_name', 'parent_head_name', 'head_level', 'active', 'transaction', 'general_ledger', 'head_type', 'budget', 'depreciation_rate', 'depreciation_rate', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function child() {
        $children = $this->hasMany($this, 'parent_head_name', 'head_name')
                ->where('status', 1)
                ->where('company_id', auth()->user()->company_id)
                ->orderBy('head_name', 'ASC');
        return $children;
    }

    public function children() {
        return $this->child()->with('children');
    }

}
