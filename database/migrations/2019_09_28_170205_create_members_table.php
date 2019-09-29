<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
            $table->unique(['first_name', 'last_name']);
        });

        Schema::create('member_run', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('run_id');
            $table->string('category');
            $table->unsignedInteger('position')->nullable();
            $table->boolean('DNF')->default(false);
            $table->boolean('DNS')->default(false);
            $table->time('time')->nullable();
            $table->time('start_time')->nullable();
            $table->unsignedInteger('startnr')->nullable();
            $table->unsignedInteger('points')->nullable();
            $table->foreign('run_id')->references('id')->on('runs');
            $table->foreign('member_id')->references('id')->on('members');
            $table->timestamps();
            $table->unique(['run_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
        Schema::dropIfExists('member_run');
    }
}
