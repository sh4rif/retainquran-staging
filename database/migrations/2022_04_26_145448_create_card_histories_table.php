<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_usr_card_history', function (Blueprint $table) {
            $table->bigIncrements('uch_id');
            $table->string('card_status');
            $table->bigInteger('usr_id')->unsigned();
            $table->bigInteger('state_id')->unsigned();
            $table->bigInteger('card_id')->unsigned();
            $table->foreign('usr_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('state_id')->references('state_id')->on('tbl_states')->onDelete('cascade');
            $table->foreign('card_id')->references('card_id')->on('tbl_cards')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_usr_card_history');
    }
}
