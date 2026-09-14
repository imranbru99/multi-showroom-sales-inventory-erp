<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_invoice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_date')->nullable();
            $table->string('invoice_no',100)->nullable();
            $table->string('collection_type',100)->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('customer_product_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_serial_no')->nullable();
            $table->string('product_name')->nullable();
            $table->string('customer_product_price',255)->nullable();
            $table->string('customer_product_model',255)->nullable();
            $table->string('customer_product_color',255)->nullable();
            $table->string('customer_product_waranty',255)->nullable();
            $table->string('qty',100)->nullable();
            $table->string('customer_product_usage_address',255)->nullable();
            $table->date('customer_product_purchase_date')->nullable();
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
        Schema::dropIfExists('tbl_invoice');
    }
}
