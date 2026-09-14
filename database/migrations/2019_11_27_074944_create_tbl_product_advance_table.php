<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblProductAdvanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_product_advance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->nullable();
            $table->string('related_product_id')->nullable();
            $table->string('pre_order_duration')->nullable();
            $table->string('shipping')->nullable();
            $table->integer('hot_discount')->nullable();
            $table->date('hot_discount_date')->nullable();
            $table->string('special_discount')->nullable();
            $table->date('special_discount_date')->nullable();
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
        Schema::dropIfExists('tbl_product_advance');
    }
}
