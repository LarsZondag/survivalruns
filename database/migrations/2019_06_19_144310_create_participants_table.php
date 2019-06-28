<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('category');
            $table->unsignedInteger('position')->nullable();
            $table->boolean('DNF')->default(false);
            $table->boolean('DNS')->default(false);
            $table->time('time')->nullable();
            $table->unsignedInteger('startnr')->nullable();
            $table->unsignedInteger('points')->nullable();
            $table->unsignedBigInteger('run_id');
            $table->foreign('run_id')->references('id')->on('runs');
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
        Schema::dropIfExists('participants');
    }
}
