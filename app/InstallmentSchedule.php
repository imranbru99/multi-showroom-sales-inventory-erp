<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstallmentSchedule extends Model {

    protected $table = "tbl_installment_schedule";
    protected $fillable = [
        'company_id', 'showroom_id', 'installment_id', 'invoice_no', 'installment_schedule_date', 'installment_schedule_amount', 'status', 'created_by', 'updated_by'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function installment() {
        return $this->hasOne(Installment::class, 'id', 'installment_id');
    }

}
