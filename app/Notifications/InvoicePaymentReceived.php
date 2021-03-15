<?php

namespace App\Notifications;

use App\EmailNotificationSetting;
use App\Invoice;
use App\InvoiceSetting;
use App\PushNotificationSetting;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class InvoicePaymentReceived extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $invoice;
    private $invoiceSetting;
    private $emailSetting;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->emailSetting = EmailNotificationSetting::where('setting_name', 'Invoice Create/Update Notification')->first();
        $this->pushNotification = PushNotificationSetting::first();
        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications) {
            array_push($via, 'mail');
        }

        if ($this->emailSetting->send_slack == 'yes') {
            array_push($via, 'slack');
        }

        if ($this->emailSetting->send_push == 'yes' && $this->pushNotification->status == 'active') {
            array_push($via, OneSignalChannel::class);
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('admin.all-invoices.index');

        return (new MailMessage)
            ->subject(__('email.invoices.paymentReceived').' - '.config('app.name'))
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line(__('email.invoices.paymentReceived').':- ')
            ->line($this->invoice->invoice_number)
            ->action(__('email.loginDashboard'), getDomainSpecificUrl(url('/login'), $notifiable->company))
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number
        ];
    }
    
    //OneSignal
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject(__('email.invoice.subject'))
            ->body(__('email.invoices.paymentReceived'));
    }
}
