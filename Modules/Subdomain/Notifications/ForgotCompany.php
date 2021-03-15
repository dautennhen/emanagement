<?php

namespace Modules\Subdomain\Notifications;

use App\Company;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotCompany extends Notification
{
    use Queueable, SmtpSettings;
    protected $company;
    protected $settings;

    /**
     * Create a new notification instance.
     *
     * @param Company $company
     * @param $settings
     */
    public function __construct(Company $company, $settings)
    {
        $this->company = $company;
        $this->settings = $settings;
        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = str_replace(request()->getHost(), $this->company->sub_domain, route('login'));
        return (new MailMessage)
            ->subject(__('subdomain::app.email.subject'))
            ->line(__('subdomain::app.email.line1'))
            ->line(__('subdomain::app.email.line2') . $this->settings->company_name)
            ->line(__('subdomain::app.email.noteLoginUrl') . ": [**$url**]($url) ")
            ->action(__('app.login'), $url)
            ->line(__('subdomain::app.email.thankYou'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
