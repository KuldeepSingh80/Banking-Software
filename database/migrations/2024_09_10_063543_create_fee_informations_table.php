<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_informations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->float('top_up_amount');
            $table->integer('levels');
            $table->integer('partners');
            $table->integer('minimum');
            $table->integer('maximum');
            $table->float('fixed_fee');
            $table->float('percentage_fee');
            $table->float('total_fee');
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
        Schema::dropIfExists('fee_informations');
    }
}
