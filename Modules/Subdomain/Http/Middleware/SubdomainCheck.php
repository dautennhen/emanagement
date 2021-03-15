<?php

namespace Modules\Subdomain\Http\Middleware;

use App\Company;
use Closure;



class SubdomainCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $host = str_replace('www.', '', request()->getHost());
        $rootCrmSubDomain = config('app.main_application_subdomain');
        // worksuite-saas.test
        $root = $this->get_domain();

        // If the main application is installed on sub_domain
        // Example main application is installed on froiden.worksuite-saas.test
        if ($rootCrmSubDomain !== null && $rootCrmSubDomain == $host) {

            // Check login page
            if (request()->route()->getName() === 'login') {
                return redirect('//' . $host . '/signin');
            }
            return $next($request);
        }

        $company = Company::where('sub_domain', $host)->first();
        $subdomain = array_first(explode('.', $host));

        // If subdomain exist is database and root is not to host
        if ($company && ($root !== $host)) {
            // Check if the url is login then do not redirect
            // https://abc.worksuite-saas.test/login

            $ignore = ['login'];
            if (in_array(request()->route()->getName(), $ignore)) {
                return $next($request);
            }

            // Otherwise redirect to login page
            // https://abc.worksuite-saas.test/home
            // https://abc.worksuite-saas.test/features
            // etc
            $loginUrl = str_replace($host, $company->sub_domain, route('login'));
            return redirect($loginUrl);
        }

        // If Home is opened home is opened in root then continue else show not found
        if (request()->route()->getName() === 'front.home') {

            if ($root == $host) {
                return $next($request);
            }
            // Show Company Not found Error Page
            abort(325);
        }

        // Check login page
        if (request()->route()->getName() === 'login') {

            // If opened login in main domain then redirect to workspace login page
            // https://worksuite-saas.test/login
            if ($root == $host) {
                return redirect('//' . $root . '/signin');
            }

            // Show Company Not found Error Page
            abort(325);
        }


        if ($subdomain == array_first(explode('.', $root))) {
            return $next($request);
        }

        // Redirect to forgot-password when from 325 page
        if (request()->route()->getName() == 'front.forgot-company') {
            return redirect('//' . $root . '/forgot-company');
        }

        // Redirect to signup when from 325 page
        if (request()->route()->getName() == 'front.signup.index') {
            return redirect('//' . $root . '/signup');
        }

        // If sub-domain do not exist in database then redirect to works
        return redirect('//' . $root . '/signin');
    }

    private function get_domain($host = false)
    {
        if(!$host){
            $host = $_SERVER['SERVER_NAME'];
        }

        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if ($count === 2) {
            if (strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
        } else if ($count > 2) {
            $myhost = $this->get_domain(explode('.', $myhost, 2)[1]);
        }
        return $myhost;
    }
}


