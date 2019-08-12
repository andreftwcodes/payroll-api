<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColToTblCaChildrens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ca_childrens', function (Blueprint $table) {
            $table->unsignedBigInteger('payslip_id')->index()->nullable()->after('ca_parents_id');

            $table->foreign('payslip_id')->references('id')->on('payslips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ca_childrens', function (Blueprint $table) {
            $table->dropForeign('ca_childrens_payslip_id_foreign');
            $table->dropColumn('payslip_id');
        });
    }
}
