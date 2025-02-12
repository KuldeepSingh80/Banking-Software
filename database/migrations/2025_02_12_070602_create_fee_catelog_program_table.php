<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeCatelogProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_catelog_program', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fees_catelog_id');
            $table->unsignedBigInteger('program_id');

            $table->foreign('fees_catelog_id')
                ->references('id')->on('fees_catelogs')->onDelete('cascade');
            $table->foreign('program_id')
                ->references('id')->on('programs')->onDelete('cascade');
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
        Schema::dropIfExists('fee_catelog_program');
    }
}
