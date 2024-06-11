<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecitersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_reciters', function (Blueprint $table) {
            $table->bigIncrements('reciter_id');
            $table->string('reciter_name');
            $table->string('reciter_country')->nullable();
            $table->string('rec_dir_name')->nullable();
            $table->string('rec_file_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_reciters');
    }
}
