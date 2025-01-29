<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInPartnerFeeSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_fee_sharings', function (Blueprint $table) {
            $table->unsignedBigInteger('partner_id')->after("sharing");
            $table->foreign('partner_id')
                ->references('id')->on('partners')
                ->onDelete('cascade');
            $table->dropColumn("partner");    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_fee_sharings', function (Blueprint $table) {
            $table->dropColumn('partner_id');
            $table->string("partner"); 
            //
        });
    }
}
