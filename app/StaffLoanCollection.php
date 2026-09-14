<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffLoanCollection extends Model
{
	protected $table = "tbl_staff_loan_collections";

	protected $fillable = [
		'showroom_id', 'staff_id', 'schedule_id', 'schedule_date', 'collection_date', 'amount', 'created_by'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];


	public function staff()
	{
		return $this->hasOne(StaffSetup::class, 'id', 'staff_id');
	}
	public function collections()
	{
		return $this->hasMany(StaffLoanCollectionList::class, 'collection_id', 'id');
	}
}
