<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_verses', function (Blueprint $table) {
            $table->bigIncrements('verse_id');
            $table->longText('verse_content');
            $table->integer('verse_number');
            $table->bigInteger('para_id')->unsigned();
            $table->foreign('para_id')->references('para_id')->on('tbl_para')->onDelete('cascade');
            $table->bigInteger('surah_id')->unsigned();
            $table->foreign('surah_id')->references('surah_id')->on('tbl_surah')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_verses');
    }
}
