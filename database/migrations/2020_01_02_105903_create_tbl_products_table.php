<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('model_no')->nullable();
            $table->string('color')->nullable();
            $table->string('uom')->nullable();
            $table->integer('price')->nullable();
            $table->integer('mrp_price')->nullable();
            $table->integer('haire_price')->nullable();
            $table->string('discount')->nullable();
            $table->integer('warranty')->nullable();
            $table->integer('reorder_level_qty')->nullable();
            $table->integer('order_by')->nullable();
            $table->integer('transport_point')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('tag_line')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
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
        Schema::dropIfExists('tbl_products');
    }
}
