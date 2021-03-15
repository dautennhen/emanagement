<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SubdomainSettings extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_domain_module_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('purchase_code')->nullable();
            $table->timestamp('supported_until')->nullable();
            $table->timestamps();
        });
        \Modules\Subdomain\Entities\SubdomainSetting::create([]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_domain_module_settings');
    }

}
