<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContributionRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution_ranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('hdr_contribution_id');
            $table->unsignedDecimal('from', 8, 2);
            $table->unsignedDecimal('to', 8, 2);
            $table->unsignedDecimal('er', 8, 2);
            $table->unsignedDecimal('ee', 8, 2);
            $table->timestamps();

            $table->foreign('hdr_contribution_id')->references('id')->on('hdr_contributions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contribution_ranges');
    }
}
