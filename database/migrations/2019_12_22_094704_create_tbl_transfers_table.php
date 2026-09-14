<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('transfer_no')->nullable();
            $table->string('date')->nullable();
            $table->string('host_type')->nullable();
            $table->string('host_id')->nullable();
            $table->string('destination_type')->nullable();
            $table->string('destination_id')->nullable();
            $table->string('total_qty')->nullable();
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
        Schema::dropIfExists('tbl_transfers');
    }
}
