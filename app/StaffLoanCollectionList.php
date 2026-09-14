<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffLoanCollectionList extends Model
{
	protected $table = "tbl_staff_loan_collection_lists";

	protected $fillable = [
		'showroom_id', 'collection_id', 'schedule_id', 'schedule_date', 'collection_amount', 'created_by'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];


	public function staff()
	{
		return $this->hasOne(StaffSetup::class, 'id', 'staff_id');
	}

	public function schedule()
	{
		return $this->hasOne(StaffLoanSchedule::class, 'id', 'schedule_id');
	}
}
