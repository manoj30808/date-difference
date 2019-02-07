<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDateCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('date_calculations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('start_date', 20);
            $table->string('end_date', 20);
            $table->boolean('end_date_included');
            $table->integer('difference');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('date_calculations');
    }
}
