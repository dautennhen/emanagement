<?php

namespace Modules\Subdomain\Listeners;

use App\Events\CompanyUrlEvent;
use App\GlobalSetting;
use App\User;
use Illuminate\Support\Facades\Notification;
use Modules\Subdomain\Notifications\CompanyUrlNotification;

class ForgotCompanyListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(CompanyUrlEvent $event)
    {
        $company = $event->company;
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', '=', 'admin');
        })
            ->where('company_id', $company->id)
            ->get();

        $setting = GlobalSetting::first();

        Notification::send($users,new CompanyUrlNotification($company,$setting));

    }
}
