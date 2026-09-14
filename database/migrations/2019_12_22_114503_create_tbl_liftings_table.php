<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLiftingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_liftings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_no')->nullable();
            $table->string('vaouchar_no')->nullable();
            $table->integer('vendor_id')->nullable();
            $table->string('store_or_showroom_type')->nullable();
            $table->integer('store_or_showroom_id')->nullable();
            $table->string('purchase_by')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('vouchar_date')->nullable();
            $table->string('total_qty')->nullable();
            $table->string('total_price')->nullable();
            $table->string('total_mrp_price')->nullable();
            $table->string('total_haire_price')->nullable();
            $table->string('discount')->nullable();
            $table->string('vat')->nullable();
            $table->string('net_amount')->nullable();
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
        Schema::dropIfExists('tbl_liftings');
    }
}
