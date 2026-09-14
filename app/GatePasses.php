<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatePasses extends Model
{
	protected $table = "tbl_gate_passes";

	// protected $fillable = [
	// 	'showroom_id', 'invoice_no', 'pass_id, 'issue_id', 'issue_no', 'created_by',
	// ];
	protected $guarded = [];

	// protected $hidden = [
	// 	'created_at', 'updated_at'
	// ];

	public function gatePass()
	{
		return $this->hasMany(GatePass::class, 'id', 'pass_id');
	}
}
