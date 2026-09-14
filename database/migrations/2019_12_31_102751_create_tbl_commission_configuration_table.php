<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCommissionConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_commission_configuration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('commission_type',100)->nullable();
            $table->integer('dealer_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('category_name',255)->nullable();
            $table->string('commission_rate',255)->nullable();
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
        Schema::dropIfExists('tbl_commission_configuration');
    }
}
