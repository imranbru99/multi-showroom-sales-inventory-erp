<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLiftingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_lifting_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lifting_id')->nullable();
            $table->integer('vendor_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('store_or_showroom_type')->nullable();
            $table->integer('store_or_showroom_id')->nullable();
            $table->string('model_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('color')->nullable();
            $table->string('qty')->nullable();
            $table->string('price')->nullable();
            $table->string('mrp_price')->nullable();
            $table->string('haire_price')->nullable();
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
        Schema::dropIfExists('tbl_lifting_products');
    }
}
