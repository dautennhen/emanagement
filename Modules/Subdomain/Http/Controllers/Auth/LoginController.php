<?php

namespace Modules\Subdomain\Http\Controllers\Auth;

use App\GlobalSetting;
use App\Http\Controllers\Front\FrontBaseController;
use App\Traits\SocialAuthSettings;
use App\User;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends FrontBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, AppBoot,SocialAuthSettings;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        if(request()->has('token')) {
            $user = User::where('social_token', request()->token)->first();

            if($user){
                $user->social_token = null;
                $user->save();

                \Auth::login($user);
                return redirect()->intended($this->redirectPath());
            }
        }
        $company = getCompanyBySubDomain();
        $this->global = GlobalSetting::first();
        $this->setting = ($company !== null) ? $company : $this->global;

        return view('auth.login', $this->data);
    }

    public function showSuperAdminLogin()
    {
        // if(!$this->isLegal()){
            // return redirect('verify-purchase');
        // }

        if(auth()->check()){
            return redirect(route('super-admin.dashboard'));
        }

        $global = $setting = GlobalSetting::first();

        return view('auth.login', compact('setting','global'));
    }

    public function getEmailVerification($code)
    {
        $this->pageTitle = __('modules.accountSettings.emailVerification');

        $company = getCompanyBySubDomain();
        $this->setting = ($company !== null) ? $company : GlobalSetting::first();

        $this->message = User::emailVerify($code);
        return view('auth.email-verification', $this->data);

    }

}
