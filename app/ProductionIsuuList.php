<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionIsuuList extends Model
{
    protected $table = "tbl_production_issue_lists";

    protected $fillable = [
        'showroom_id','issue_id','product_id','product_name','model_no','serial_no','color','price','qty','status','created_by','updated_by'
    ];

	protected $hidden = [
		'created_at','updated_at'
	];

    /**
     * Get the issue associated with the ProductIssueList
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function issue()
    {
        return $this->hasOne(ProductionIssue::class, 'id', 'issue_id');
    }

    /**
     * Get the product associated with the ProductIssueList
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
