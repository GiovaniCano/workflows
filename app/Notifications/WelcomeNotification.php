<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $app_name;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->app_name = config('app.name');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject(__('mail.welcome.greeting', ['app_name' => $this->app_name]))
                    ->greeting(__('mail.welcome.greeting', ['app_name' => $this->app_name]))
                    ->line('Nunc consequat tortor convallis, eleifend dolor in, condimentum felis.')
                    ->action(__('mail.welcome.action'), route('workflow.index'))
                    ->line('Ut vel nisi at tortor tincidunt tincidunt ut nec risus.')
                    ->line(__('mail.welcome.thanks'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
