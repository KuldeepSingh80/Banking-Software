<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInFeesCatelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fees_catelogs', function (Blueprint $table) {
            $table->string('sender_pay')
                  ->after('payer')
                  ->nullable();
            $table->string('receiver_pay')
                  ->after('sender_pay')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees_catelogs', function (Blueprint $table) {
            $table->dropColumn('sender_pay');
            $table->dropColumn('receiver_pay');
        });
    }
}
