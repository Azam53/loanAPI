<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('loan_request_id');
            $table->date('payment_date');
            $table->float('payment_amount');
            $table->smallInteger('payment_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_loan_payments');
    }
}
