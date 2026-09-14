<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCashCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cash_collection', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->string('collection_no',255);
            $table->string('invoice_amount',255)->nullable();
            $table->string('previous_collection',255)->nullable();
            $table->date('collection_date')->nullable();
            $table->string('collection_amount',255)->nullable();
            $table->string('current_due',255)->nullable();
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
        Schema::dropIfExists('tbl_cash_collection');
    }
}
