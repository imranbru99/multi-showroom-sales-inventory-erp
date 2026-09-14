<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTransferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_transfer_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transfer_id')->nullable();
            $table->integer('vendor_id')->nullable();
            $table->integer('lifting_product_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('name')->nullable();
            $table->string('model_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('color')->nullable();
            $table->string('qty')->nullable();
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
        Schema::dropIfExists('tbl_transfer_products');
    }
}
