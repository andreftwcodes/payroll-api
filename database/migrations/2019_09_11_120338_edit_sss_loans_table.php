<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSssLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sss_loans', function (Blueprint $table) {
            $table->renameColumn('loan_no', 'ref_no');
            $table->dropColumn('payment_terms');
            $table->renameColumn('date_loaned', 'loaned_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sss_loans', function (Blueprint $table) {
            $table->renameColumn('ref_no', 'loan_no');
            $table->unsignedInteger('payment_terms')->after('amortization_amount');
            $table->renameColumn('loaned_at', 'date_loaned');
        });
    }
}
