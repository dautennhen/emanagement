<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function markAllRead()
    {
        $this->user->unreadNotifications->markAsRead();
        return Reply::success(__('messages.notificationRead'));
    }

    public function showAdminNotifications()
    {
        if(DB::table('companies')->where('id',Auth::user()->company_id)->select('locale')->first()->locale=='vn'){
            $lang = 'vi';
        } else {
            $lang = 'en';
        }
        Carbon::setLocale($lang);
        
        $view = view('notifications.admin_user_notifications', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $view]);
    }
    
    public function showUserNotifications()
    {
        if(DB::table('users')->where('id',Auth::user()->id)->select('locale')->first()->locale=='vn'){
            $lang = 'vi';
        } else {
            $lang = 'en';
        }
        Carbon::setLocale($lang);
        $view = view('notifications.user_notifications', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $view]);
    }

    public function showClientNotifications()
    {
        if(DB::table('users')->where('id',Auth::user()->id)->select('locale')->first()->locale=='vn'){
            $lang = 'vi';
        } else {
            $lang = 'en';
        }
        Carbon::setLocale($lang);
        $view = view('notifications.client_notifications', $this->data)->render();
        
        return Reply::dataOnly(['status' => 'success', 'html' => $view]);
    }

    public function showAllMemberNotifications()
    {
        
        return view('notifications.member.all_notifications', $this->data);
    }

    public function showAllClientNotifications()
    {
        return view('notifications.client.all_notifications', $this->data);
    }

    public function showAllAdminNotifications()
    {
        
        return view('notifications.admin.all_notifications', $this->data);
    }

    public function showAllSuperAdminNotifications()
    {
        return view('notifications.superadmin.all_notifications', $this->data);
    }
}
