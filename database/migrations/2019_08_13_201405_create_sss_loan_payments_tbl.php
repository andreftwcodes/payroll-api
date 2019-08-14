<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSssLoanPaymentsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sss_loan_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sss_loan_id')->index();
            $table->unsignedBigInteger('payslip_id')->index()->unique()->nullable();
            $table->date('paid_at');
            $table->timestamps();

            $table->foreign('sss_loan_id')->references('id')->on('sss_loans')->onDelete('cascade');
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
        Schema::dropIfExists('sss_loan_payments');
    }
}
