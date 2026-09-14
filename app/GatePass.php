<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
	protected $table = "tbl_gate_pass";

	protected $fillable = [
		'showroom_id', 'dealer_id', 'invoice_no', 'date', 'created_by',
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];

	public function dealer()
	{
		return $this->hasOne(DealerSetup::class, 'id', 'dealer_id');
	}
	public function gatePasses()
	{
		return $this->hasMany(GatePasses::class, 'pass_id', 'id');
	}
}
