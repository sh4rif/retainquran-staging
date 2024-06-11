<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_translators', function (Blueprint $table) {
            $table->bigIncrements('trans_id');
            $table->string('trans_author')->nullable();
            $table->string('trans_dir_name')->nullable();
            $table->string('trans_file_name')->nullable();
            $table->bigInteger('translation_id')->unsigned();
            $table->foreign('translation_id')->references('translation_id')->on('tbl_translations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_translators');
    }
}
