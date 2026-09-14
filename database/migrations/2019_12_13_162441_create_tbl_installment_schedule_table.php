<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInstallmentScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_installment_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('installment_id');
            $table->string('invoice_no',255)->nullable();
            $table->date('installment_schedule_date')->nullable();
            $table->string('installment_schedule_amount',255)->nullable();
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_installment_schedule');
    }
}
