<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCustomerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_customer_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('product_id');
            $table->integer('reference_id')->nullable();
            $table->integer('showroom_id')->nullable();
            $table->integer('qty')->default('1');
            $table->integer('warranty')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('remarks',50)->nullable();
            $table->string('purchase_type',50)->nullable();
            $table->string('installment_type',255)->nullable();
            $table->string('product_model',255)->nullable();
            $table->string('cash_price',255)->nullable();
            $table->string('mrp_price',255)->nullable();
            $table->string('deposite',255)->nullable();
            $table->string('installment_price',255)->nullable();
            $table->string('total_installment',50)->nullable();
            $table->string('monthly_installment_amount',255)->nullable();
            $table->string('product_usage_address',255)->nullable();
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
        Schema::dropIfExists('tbl_customer_products');
    }
}
