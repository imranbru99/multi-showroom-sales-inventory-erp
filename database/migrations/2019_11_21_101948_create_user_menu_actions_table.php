<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMenuActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_menu_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parentmenuId');
            $table->integer('menuType');
            $table->string('actionName');
            $table->string('actionLink');
            $table->integer('orderBy');
            $table->integer('actionStatus');
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
        Schema::dropIfExists('user_menu_actions');
    }
}
