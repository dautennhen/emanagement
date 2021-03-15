<?php

if (!function_exists('getCompanyBySubDomain')) {
    function getCompanyBySubDomain()
    {
        $company = App\Company::where('sub_domain', request()->getHost())->first();

        if($company) {
            return $company;
        }

        return null;
    }
}
