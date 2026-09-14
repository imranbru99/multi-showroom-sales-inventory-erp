<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPaymentToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_payment_to_company', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id');
            $table->string('payment_no');
            $table->string('payment_date')->nullable();
            $table->string('current_due')->nullable();
            $table->string('payment_now')->nullable();
            $table->string('balance')->nullable();
            $table->string('money_receipt')->nullable();
            $table->string('payment_type')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('tbl_payment_to_company');
    }
}
