<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagibigTableContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagibig_table_contributions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedDecimal('from', 8, 2);
            $table->unsignedDecimal('to', 8, 2);
            $table->unsignedDecimal('er', 8, 2);
            $table->unsignedDecimal('ee', 8, 2);
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
        Schema::dropIfExists('pagibig_table_contributions');
    }
}
