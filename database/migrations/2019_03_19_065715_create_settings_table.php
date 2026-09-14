<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id',10);
            $table->text('siteLogo')->nullable();
            $table->text('adminLogo')->nullable();
            $table->text('mobile1')->nullable();
            $table->text('mobile2')->nullable();
            $table->text('siteEmail1')->nullable();
            $table->text('siteEmail2')->nullable();
            $table->text('siteAddress1')->nullable();
            $table->text('siteAddress2')->nullable();
            $table->integer('sitestatus')->nullable();
           
            $table->text('metaTitle')->nullable();
            $table->text('metaKeyword')->nullable();
            $table->text('metaDescription')->nullable();
            $table->integer('orderBy')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
