<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLiftingReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_lifting_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->nullable();
            $table->string('store_or_showroom_type')->nullable();
            $table->integer('store_or_showroom_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('date')->nullable();
            $table->string('total_qty')->nullable();
            $table->string('total_price')->nullable();
            $table->string('total_mrp_price')->nullable();
            $table->string('total_haire_price')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('tbl_lifting_returns');
    }
}
