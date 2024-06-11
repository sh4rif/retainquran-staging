<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_translations', function (Blueprint $table) {
            $table->bigIncrements('translation_id');
            $table->string('translation_language');
            $table->string('translation_country');            
            $table->string('translation_dir_name')->nullable();
            $table->string('translation_file_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_translations');
    }
}
