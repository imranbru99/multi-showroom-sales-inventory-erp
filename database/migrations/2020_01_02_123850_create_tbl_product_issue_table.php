<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblProductIssueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_product_issue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requisition_id')->nullable();
            $table->integer('dealer_id')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('issue_no')->nullable();
            $table->string('date')->nullable();
            $table->string('total_qty')->nullable();
            $table->string('total_amount')->nullable();
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
        Schema::dropIfExists('tbl_product_issue');
    }
}
