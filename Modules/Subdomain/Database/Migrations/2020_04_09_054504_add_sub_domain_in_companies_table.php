<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubDomainInCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('sub_domain')->after('id')->nullable();
        });
        $companies = \App\Company::withoutGlobalScope('active')->get();

        foreach ($companies as $company) {
            $companyName= array_first(explode(' ',$company->company_name));
            $company->sub_domain =  $companyName.'.' . isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'worksuite-saas.test';
            $company->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('sub_domain');
        });
    }
}
