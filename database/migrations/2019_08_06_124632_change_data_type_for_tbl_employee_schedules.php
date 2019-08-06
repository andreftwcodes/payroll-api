<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDataTypeForTblEmployeeSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->time('start_1')->nullable()->change();
            $table->time('end_1')->nullable()->change();
            $table->time('start_2')->nullable()->change();
            $table->time('end_2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->time('start_1')->change();
            $table->time('end_1')->change();
            $table->time('start_2')->change();
            $table->time('end_2')->change();
        });
    }
}
