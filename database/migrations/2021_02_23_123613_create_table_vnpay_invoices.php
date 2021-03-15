<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableVnpayInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vnpay_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('package_id');
            $table->string('transaction_id', 255)->nullable();
            $table->string('amount', 255)->nullable();
            $table->date('pay_date')->nullable();
            $table->date('next_pay_date')->nullable();
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
        Schema::dropIfExists('table_vnpay_invoices');
    }
}
