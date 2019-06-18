<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunEditionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('run_editions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('uvponline_id')->unique()->nullable();
            $table->date('date');
            $table->date('enrollment_start_date')->nullable();
            $table->boolean('LSR')->default(false);
            $table->boolean('MSR')->default(false);
            $table->boolean('KSR')->default(false);
            $table->boolean('JSR')->default(false);
            $table->boolean('qualification_run')->default(false);
            $table->integer('year');
            $table->string('distances');
            $table->unsignedBigInteger('run_id');
            $table->foreign('run_id')->references('id')->on('runs');
            $table->unique(['year', 'run_id']);
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
        Schema::dropIfExists('run_editions');
    }
}
