<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChargeTypeColumnInFeeInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_informations', function (Blueprint $table) {
            $table->enum('charges_type', ['fixed', 'percentage'])
                  ->after('top_up_amount')
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
            $table->dropColumn('charges_type');
        });
    }
}
