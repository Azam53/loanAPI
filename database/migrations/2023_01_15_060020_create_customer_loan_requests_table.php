<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_loan_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('loan_id');
            $table->float('loan_amount');
            $table->integer('term_duration');
            $table->date('loan_applied_date');
            $table->smallInteger('loan_paid_status')->default(0);
            $table->smallInteger('loan_status')->default(0);
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
        Schema::dropIfExists('customer_loan_requests');
    }
}
