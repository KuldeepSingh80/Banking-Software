<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInFeeInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_informations', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->after("partners")->nullable();
            $table->foreign('merchant_id')
                ->references('id')->on('merchants')
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
        Schema::table('fee_informations', function (Blueprint $table) {
            $table->dropColumn('merchant_id');
        });
    }
}
