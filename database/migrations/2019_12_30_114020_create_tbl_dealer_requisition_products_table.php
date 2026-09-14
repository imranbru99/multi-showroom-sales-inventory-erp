<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDealerRequisitionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dealer_requisition_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requisition_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->text('product_name')->nullable();
            $table->text('model_no')->nullable();
            $table->text('price')->nullable();
            $table->text('qty')->nullable();
            $table->text('amount')->nullable();
            $table->text('approved_qty')->nullable();
            $table->text('approved_amount')->nullable();
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
        Schema::dropIfExists('tbl_dealer_requisition_products');
    }
}
