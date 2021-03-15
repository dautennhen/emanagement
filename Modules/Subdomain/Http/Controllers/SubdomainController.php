<?php

namespace Modules\Subdomain\Http\Controllers;

use App\Company;
use App\Events\CompanyUrlEvent;
use App\Feature;
use App\FrontClients;
use App\FrontDetail;
use App\Helper\Reply;
use App\Http\Controllers\Front\FrontBaseController;
use App\Module;
use App\Testimonials;
use App\User;
use Illuminate\Http\Request;
use Modules\Subdomain\Http\Requests\Auth\CheckSubdomainRequest;
use Modules\Subdomain\Http\Requests\Auth\ForgotCompanyRequest;
use Modules\Subdomain\Notifications\ForgotCompany;

class SubdomainController extends FrontBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param null $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */


    public function workspace()
    {
        $this->pageTitle = __('subdomain::app.core.workspaceTitle');
        $view = ($this->setting->front_design == 1) ? 'subdomain::saas.workspace' : 'subdomain::workspace';
        return view($view, $this->data);
    }
    public function forgotCompany()
    {
        $this->pageTitle = __('subdomain::app.core.forgotCompanyTitle');
        $view = ($this->setting->front_design) == 1 ? 'subdomain::saas.forgot-company' : 'subdomain::forgot-company';
        return view($view, $this->data);
    }

    public function submitForgotCompany(ForgotCompanyRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user) {

            if(!$user->company){
                return Reply::error('No company linked with this email');
            }

            $user->notify(new ForgotCompany($user->company, $this->setting));
            return Reply::success(__('subdomain::app.messages.forgetMailSuccess'));
        }

        return Reply::error(__('subdomain::app.messages.forgetMailFail'));
    }

    public function checkDomain(CheckSubdomainRequest $request)
    {
        return Reply::redirect(str_replace(request()->getHost(), $request->sub_domain.'.'.get_domain(), route('login')));
    }
    public function notifyDomain(Request $request)
    {
        $company = Company::findOrFail($request->company_id);
        event(new CompanyUrlEvent($company));

        return Reply::success('Successfully notified to all admins');
    }


    public function iframe()
    {
        $this->user =  \user();
        $this->pushSetting= \App\PushNotificationSetting::first();
        return view('subdomain::iframe',$this->data);
    }
}
