<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_surah', function (Blueprint $table) {
            $table->bigIncrements('surah_id');
            $table->string('surah_name');
            $table->integer('surah_number')->unique();
            $table->integer('surah_total_verses');
            $table->bigInteger('para_id')->unsigned();
            $table->foreign('para_id')->references('para_id')->on('tbl_para')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_surah');
    }
}
