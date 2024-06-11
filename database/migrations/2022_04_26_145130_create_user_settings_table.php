<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_user_settings', function (Blueprint $table) {
            $table->bigIncrements('us_id');
            $table->bigInteger('usr_id')->unsigned();
            $table->foreign('usr_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('reciter_id')->unsigned();
            $table->foreign('reciter_id')->references('reciter_id')->on('tbl_reciters')->onDelete('cascade');
            $table->bigInteger('trans_id')->unsigned();
            $table->foreign('trans_id')->references('trans_id')->on('tbl_translators')->onDelete('cascade');
             $table->bigInteger('rtype_id')->unsigned();
            $table->foreign('rtype_id')->references('rtype_id')->on('tbl_reading_types')->onDelete('cascade');
            $table->bigInteger('translation_id')->unsigned();
            $table->foreign('translation_id')->references('translation_id')->on('tbl_translations')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_user_settings');
    }
}
