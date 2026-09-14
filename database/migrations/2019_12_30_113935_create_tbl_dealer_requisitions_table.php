<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDealerRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dealer_requisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dealer_id')->nullable();
            $table->text('requisition_no')->nullable();
            $table->text('date')->nullable();
            $table->integer('product_id')->nullable();
            $table->text('total_qty')->nullable();
            $table->text('total_amount')->nullable();
            $table->integer('approved_by')->nullable();
            $table->text('total_approve_qty')->nullable();
            $table->text('total_approve_amount')->nullable();
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
        Schema::dropIfExists('tbl_dealer_requisitions');
    }
}
