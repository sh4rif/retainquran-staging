<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLastViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('last_view', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->bigInteger('user_id');
            $table->string('view_type')->nullable();
            $table->bigInteger('page_no')->nullable();
            $table->bigInteger('para_id')->nullable();
            $table->bigInteger('surah_id')->nullable();
            $table->bigInteger('verse_id')->nullable();
            $table->integer('surah_number')->nullable();
            $table->boolean('is_juz')->nullable();
            $table->integer('juz_number')->nullable();
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
        Schema::dropIfExists('last_view');
    }
}
