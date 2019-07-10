<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSchedTimeDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->datetime('sched_start_1')->nullabe(false)->change();
            $table->datetime('sched_end_1')->nullabe(false)->change();
            $table->datetime('sched_start_2')->nullabe(false)->change();
            $table->datetime('sched_end_2')->nullabe(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('sched_start_1')->change();
            $table->time('sched_end_1')->change();
            $table->time('sched_start_2')->change();
            $table->time('sched_end_2')->change();
        });
    }
}
