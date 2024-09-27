<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransationCategoryColumnInFeeInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_informations', function (Blueprint $table) {
            $table->enum('transaction_category', ['deposit', 'withdraw'])
                  ->after('total_fee')
                  ->nullable();
            $table->enum('payer', ['sender', 'receiver', 'split'])
                  ->after('transaction_category')
                  ->nullable();
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
        Schema::table('fee_informations', function (Blueprint $table) {
            $table->dropColumn('transaction_category');
            $table->dropColumn('payer');
            $table->dropColumn('sender_pay');
            $table->dropColumn('receiver_pay');
        });
    }
}
