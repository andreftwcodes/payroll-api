<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('sched_start_2')->after('sched_end');
            $table->time('sched_end_2')->after('sched_start_2');

            $table->renameColumn('sched_start', 'sched_start_1');
            $table->renameColumn('sched_end', 'sched_end_1');

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
            $table->dropColumn(['sched_start_2', 'sched_end_2']);
            $table->renameColumn('sched_start_1', 'sched_start');
            $table->renameColumn('sched_end_1', 'sched_end');
        });
    }
}
