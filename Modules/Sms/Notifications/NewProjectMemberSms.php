<?php

namespace Modules\Sms\Notifications;

use App\EmailNotificationSetting;
use App\ProjectMember;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Modules\Sms\Http\Traits\WhatsappMessageTrait;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewProjectMemberSms extends Notification
{
    use Queueable, WhatsappMessageTrait;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $user;
    private $member;
    public function __construct(ProjectMember $member)
    {
        $user = User::findOrFail($member->user_id);
        $this->user = $user;
        $this->member = $member;
        $this->emailSetting = EmailNotificationSetting::where('setting_name', 'Employee Assign to Project')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = array();
        if ($this->emailSetting->send_twilio == 'yes' && !is_null($notifiable->mobile) && !is_null($notifiable->country_id)) {
            if (sms_setting()->status) {
                array_push($via, TwilioChannel::class);
            }
            if (sms_setting()->nexmo_status) {
                array_push($via, 'nexmo');
            }
            if (sms_setting()->msg91_status) {
                array_push($via, 'msg91');
            }
        }
        return $via;
    }

    public function toTwilio($notifiable)
    {
        $message = __('email.newProjectMember.subject') . ' - ' . ucwords($this->member->project->project_name);
        $this->toWhatsapp($notifiable, $message);

        if (sms_setting()->status) {
            return (new TwilioSmsMessage())
                ->content($message);
        }
    }

    public function toNexmo($notifiable)
    {
        $message = __('email.newProjectMember.subject') . ' - ' . ucwords($this->member->project->project_name);
        
        if (sms_setting()->nexmo_status) {
            return (new NexmoMessage())
                ->content($message);
        }
    }

    public function toMsg91($notifiable)
    {
        $message = __('email.newProjectMember.subject') . ' - ' . ucwords($this->member->project->project_name);
        
        if (sms_setting()->msg91_status) {
            return (new \Craftsys\Notifications\Messages\Msg91SMS)
                ->from(sms_setting()->msg91_from)
                ->content($message);
        }
    }
}
