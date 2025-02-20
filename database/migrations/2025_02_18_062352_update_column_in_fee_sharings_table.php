<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnInFeeSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_sharings', function (Blueprint $table) {
            $table->unsignedBigInteger('base_cost_partner_id')->after("sharing_level")->nullable();

            $table->foreign('base_cost_partner_id')
                ->references('id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_sharings', function (Blueprint $table) {
            //
        });
    }
}
