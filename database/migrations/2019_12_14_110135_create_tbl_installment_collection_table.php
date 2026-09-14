<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInstallmentCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_installment_collection', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('installment_id');
            $table->integer('customer_product_id');
            $table->integer('customer_id');
            $table->integer('product_id');
            $table->string('invoice_no',255)->nullable();
            $table->string('customer_name',255)->nullable();
            $table->string('installment_price',255)->nullable();
            $table->string('booking_amount',255)->nullable();
            $table->string('installment_qty',255)->nullable();
            $table->string('installment_amount',255)->nullable();
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
        Schema::dropIfExists('tbl_installment_collection');
    }
}
