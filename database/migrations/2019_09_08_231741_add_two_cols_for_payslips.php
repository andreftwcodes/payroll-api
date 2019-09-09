<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTwoColsForPayslips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            Schema::table('payslips', function (Blueprint $table) {
                $table->date('from')->default(today())->after('employee_id');
                $table->date('to')->default(today())->after('from');
            });

            DB::statement('ALTER TABLE payslips ALTER `from` DROP DEFAULT, ALTER `to` DROP DEFAULT');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn(['from', 'to']);
        });
    }
}
