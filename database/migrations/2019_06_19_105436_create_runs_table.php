<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->boolean('enrollment_open')->default(false);
            $table->date('enrollment_start_date')->nullable();
            $table->boolean('LSR')->default(false);
            $table->boolean('MSR')->default(false);
            $table->boolean('KSR')->default(false);
            $table->boolean('JSR')->default(false);
            $table->boolean('qualification_run')->default(false);
            $table->integer('year');
            $table->string('distances');
            $table->unsignedBigInteger('organiser_id');
            $table->foreign('organiser_id')->references('id')->on('organisers');
            $table->unsignedBigInteger('uvponline_id')->unique()->nullable();
            $table->unsignedBigInteger('uvponline_results_id')->unique()->nullable();
            $table->unique(['organiser_id', 'date']);
            $table->timestamp('details_updated')->nullable();
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
        Schema::dropIfExists('runs');
    }
}
