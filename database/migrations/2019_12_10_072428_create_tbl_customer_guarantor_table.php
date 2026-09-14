<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCustomerGuarantorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_customer_guarantor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('product_id')->nullable();
            $table->string('gurantor_name',255)->nullable();
            $table->string('gurantor_phone_no',255)->nullable();
            $table->string('gurantor_age',50)->nullable();
            $table->string('guarantor_marital_status',50)->nullable();
            $table->string('guarantor_spouse_name',255)->nullable();
            $table->string('guarantor_father_name',255)->nullable();
            $table->string('guarantor_present_address',255)->nullable();
            $table->string('guarantor_permanent_address',255)->nullable();
            $table->string('guarantor_profession_name',255)->nullable();
            $table->string('guarantor_designation',255)->nullable();
            $table->string('guarantor_workplace_phone_no',255)->nullable();
            $table->string('guarantor_monthly_income',255)->nullable();
            $table->string('guarantor_work_place_address',255)->nullable();
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
        Schema::dropIfExists('tbl_customer_guarantor');
    }
}
