<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_sharings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fee_id');
            $table->integer('sharing_level');
            $table->float('fixed_base_cost');
            $table->float('percentage_base_cost');
            $table->float('fixed_markup');
            $table->float('percentage_markup');
            $table->float('fixed_markup_base_cost');
            $table->float('percentage_markup_base_cost');
            $table->timestamps();

            $table->foreign('fee_id')
                ->references('id')->on('fee_informations')
                ->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_sharings');
    }
}
