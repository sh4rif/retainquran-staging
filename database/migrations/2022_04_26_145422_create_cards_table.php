<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cards', function (Blueprint $table) {
            $table->bigIncrements('card_id');
            $table->string('card_name');
            $table->dateTime('due_at');
            $table->bigInteger('usr_id')->unsigned();
            $table->foreign('usr_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('state_id')->unsigned();
            $table->foreign('state_id')->references('state_id')->on('tbl_states')->onDelete('cascade');
            $table->bigInteger('surah_id')->unsigned();
            $table->foreign('surah_id')->references('surah_id')->on('tbl_surah')->onDelete('cascade');
            $table->bigInteger('verse_id')->unsigned();
            $table->foreign('verse_id')->references('verse_id')->on('tbl_verses')->onDelete('cascade');
            $table->bigInteger('deck_id')->unsigned();
            $table->foreign('deck_id')->references('deck_id')->on('tbl_decks')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_cards');
    }
}
