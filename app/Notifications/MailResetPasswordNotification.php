<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class MailResetPasswordNotification extends Notification
{
    use Queueable;
    public $link;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $usr_name = '';
        if(session('usr_name')){
             $usr_name  = session('usr_name');
         }
        return (new MailMessage)
            ->greeting('Hello '.($usr_name).'!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Password Reset Token: '. $this->link)
            ->line('If you did not request a password reset, no further action is required.')
            ->line('Kind Regards,')
            ->salutation(new HtmlString('<strong>Retain Quran</strong>'));
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
            //
        ];
    }
}
