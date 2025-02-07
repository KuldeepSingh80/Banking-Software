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
            $table->unsignedBigInteger('fees_catalog_id')->nullable()->after('id')->nullable();
            $table->foreign('fees_catalog_id')
                ->references('id')->on('fees_catelogs');
            $table->string("fees_id")->after("fees_catalog_id")->nullable();
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
            $table->dropColumn('fees_catalogs_id')->nullable()->after('total_fee');
            $table->dropColumn("fees_id");
            //
        });
    }
}
