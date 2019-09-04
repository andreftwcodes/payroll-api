<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHiredAtColToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            Schema::table('employees', function (Blueprint $table) {
                $table->date('hired_at')->default(today())->after('payment_period');
            });

            DB::statement('ALTER TABLE employees ALTER hired_at DROP DEFAULT');
        
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('hired_at');
        });
    }
}
