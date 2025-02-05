<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeesCatelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees_catelogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("fees_id");
            $table->string("name");
            $table->string("description");
            $table->enum('payer', ['sender', 'receiver', 'split', 'user'])
                  ->nullable();
            $table->string('unit_of_measure');
            $table->unsignedBigInteger('transaction_id');
            $table->foreign('transaction_id')
                ->references('id')->on('transaction_categories');
            $table->enum('charges_type', ['fixed', 'percentage'])
                      ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees_catelogs');
    }
}
