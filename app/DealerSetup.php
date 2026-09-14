<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerSetup extends Model {

    protected $table = "tbl_dealers";
    protected $fillable = [
        'company_id', 'showroom_id', 'region_id', 'area_id', 'territory_id', 'type', 'code', 'commission', 'name', 'short_name', 'document', 'contact_person', 'mobile', 'email', 'address', 'courier_address', 'credit_limit', 'reference_by', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Get all of the issue for the DealerSetup
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issue() {
        return $this->hasMany(ProductIssue::class, 'dealer_id', 'id');
    }

    public function documents() {
        return $this->hasMany(DealerDocument::class, 'dealer_id', 'id');
    }

}
