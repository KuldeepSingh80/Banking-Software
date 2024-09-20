<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerFeeSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_fee_sharings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sharing_level_id');
            $table->float('sharing');
            $table->string('partner');
            $table->float('fixed_cost');
            $table->float('percentage_cost');
            $table->timestamps();

            $table->foreign('sharing_level_id')
                ->references('id')->on('fee_sharings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_fee_sharings');
    }
}
