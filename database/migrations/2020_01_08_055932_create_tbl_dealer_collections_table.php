<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDealerCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dealer_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_issue_id')->nullable();
            $table->integer('dealer_id')->nullable();
            $table->string('payment_no',255)->nullable();
            $table->string('payment_date')->nullable();
            $table->string('money_receipt_no',255)->nullable();
            $table->string('money_receipt_type',255)->nullable();
            $table->integer('payment_amount')->nullable();
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
        Schema::dropIfExists('tbl_dealer_collections');
    }
}
