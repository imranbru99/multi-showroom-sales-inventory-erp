<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CloseAccount extends Model
{
	protected $table = "close_account";

	protected $fillable = [
		'showroom_id', 'project_id', 'customer_id', 'customer_code', 'sale_id', 'invoice_no', 'invoice_amount', 'collection_amount', 'close_date', 'cam_due', 'interest_receive',
		'discount_amount', 'employee_id', 'missing_amount', 'late_days', 'late_fee', 'is_schedule', 'is_settle', 'created_by', 'remarks'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];

	public function customer()
	{
		return $this->hasOne(CustomerRegistrationSetup::class, 'id', 'customer_id');
	}

	public function sale()
	{
		return $this->hasOne(RetailSale::class, 'id', 'sale_id');
	}

	public function collection()
	{
		return $this->hasOne(InstallmentCollection::class, 'id', 'customer_id');
	}

	public function staff()
	{
		return $this->hasOne(StaffSetup::class, 'id', 'employee_id');
	}

	public function agreement()
	{
		return $this->hasOne(CustomerAgreement::class, 'customer_id', 'customer_id');
	}

	public function user()
	{
		return $this->hasOne(Admin::class, 'id', 'created_by');
	}
}
