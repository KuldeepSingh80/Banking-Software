<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateColumnsInFeeInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_informations', function (Blueprint $table) {
            DB::statement("ALTER TABLE `fee_informations` DROP COLUMN `transaction_category`");
            DB::statement("ALTER TABLE `fee_informations` DROP COLUMN `payer`");
            $table->unsignedBigInteger('transaction_category_id')->nullable()->after('total_fee');
            $table->foreign('transaction_category_id')
                ->references('id')->on('transaction_categories');
            $table->enum('payer', ['sender', 'receiver', 'split', 'user'])->after('transaction_category_id');
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
