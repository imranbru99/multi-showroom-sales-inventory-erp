<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblProductIssueListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_product_issue_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('issue_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('model_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('commission_rate')->nullable();
            $table->string('price')->nullable();
            $table->string('qty')->nullable();
            $table->string('amount')->nullable();
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
        Schema::dropIfExists('tbl_product_issue_lists');
    }
}
