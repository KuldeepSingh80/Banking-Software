<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnInFeeInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_informations', function (Blueprint $table) {
            $table->unsignedBigInteger('fees_config_id')->after("id")->nullable();

            $table->foreign('fees_config_id')
                ->references('id')->on('fees_configures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_informations', function (Blueprint $table) {
            //
        });
    }
}
