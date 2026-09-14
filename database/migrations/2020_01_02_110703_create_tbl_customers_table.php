<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',100)->nullable();
            $table->string('name',255);
            $table->string('nick_name',255)->nullable();
            $table->string('nid',255)->nullable();
            $table->integer('age')->nullable();
            $table->string('phone_no',255)->nullable();
            $table->string('marital_status',100)->nullable();
            $table->string('spouse_name',255)->nullable();
            $table->string('fathers_name',255)->nullable();
            $table->string('mothers_name',255)->nullable();
            $table->string('gender',50)->nullable();
            $table->string('current_residence',50)->nullable();
            $table->string('residence_duration',50)->nullable();
            $table->string('total_family_member',50)->nullable();
            $table->string('present_address',255)->nullable();
            $table->string('permanent_address',255)->nullable();
            $table->string('profession_name',255)->nullable();
            $table->string('profession_duration',50)->nullable();
            $table->string('total_earning_member',50)->nullable();
            $table->string('designation',255)->nullable();
            $table->string('monthly_income',255)->nullable();
            $table->string('work_place_address',255)->nullable();
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
        Schema::dropIfExists('tbl_customers');
    }
}
