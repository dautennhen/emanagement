<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVnpaySuperadmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stripe_setting', function (Blueprint $table) {
            $table->enum('vnpay_status', ['active', 'inactive'])->default('inactive');
            $table->string('vnp_TmnCode', 255)->nullable();
            $table->string('vnp_HashSecret', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stripe_setting', function (Blueprint $table) {
            $table->dropColumn(['vnpay_status']);
            $table->dropColumn(['vnp_TmnCode']);
            $table->dropColumn(['vnp_HashSecret']);
        });
    }
}
