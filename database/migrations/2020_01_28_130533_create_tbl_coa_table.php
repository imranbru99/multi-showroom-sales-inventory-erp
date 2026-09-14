<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCoaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_coa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('head_code')->nullable();
            $table->string('head_name')->nullable();
            $table->string('parent_head_name')->nullable();
            $table->string('head_level')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('transaction')->default(0);
            $table->tinyInteger('general_ledger')->default(0);
            $table->string('head_type')->nullable();
            $table->tinyInteger('budget')->defualt(0);
            $table->tinyInteger('depreciation')->defualt(0);
            $table->string('depreciation_rate')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('tbl_coa');
    }
}
