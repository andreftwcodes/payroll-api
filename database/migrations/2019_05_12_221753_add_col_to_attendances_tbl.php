<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColToAttendancesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedDecimal('amount', 8, 2)->after('locale_id');
            $table->time('sched_start')->after('amount');
            $table->time('sched_end')->after('sched_start');
            $table->boolean('special_person')->after('sched_end');
            $table->boolean('night_shift')->after('special_person');
            $table->boolean('overtime')->after('night_shift');
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
            $table->dropColumn([
                'amount',
                'sched_start',
                'sched_end',
                'special_person',
                'night_shift',
                'overtime'
            ]);
        });
    }
}
