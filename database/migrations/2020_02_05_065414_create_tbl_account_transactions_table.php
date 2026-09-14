<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_account_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('voucher_no')->nullable();
            $table->string('voucher_type')->nullable();
            $table->string('voucher_date')->nullable();
            $table->string('coa_id')->nullable();
            $table->string('coa_head_code')->nullable();
            $table->string('showroom_id')->nullable();
            $table->string('narration')->nullable();
            $table->string('debit_amount')->nullable();
            $table->string('credit_amount')->nullable();
            $table->string('posted')->nullable();
            $table->tinyInteger('approve')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('delete')->default(0);
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
        Schema::dropIfExists('tbl_account_transactions');
    }
}
