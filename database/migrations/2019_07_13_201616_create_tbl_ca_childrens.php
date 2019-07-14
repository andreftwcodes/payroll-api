<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCaChildrens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_childrens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ca_parents_id')->index();
            $table->date('date');
            $table->unsignedDecimal('credit', 8, 2)->nullable();
            $table->unsignedDecimal('debit', 8, 2)->nullable();
            $table->timestamps();
            $table->foreign('ca_parents_id')->references('id')->on('ca_parents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ca_childrens');
    }
}
