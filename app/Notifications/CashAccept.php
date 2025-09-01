<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

use Illuminate\Notifications\Notification;

class CashAccept extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $admin;
    public $cash;
    public function __construct($admin,$cash)
    {
        $this->admin=$admin;
        $this->cash=$cash;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
     return ['database', 'broadcast'];

    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }
        public function toDatabase($notifiable)
    {
        return [
            'notification_type'=>'cash_for_accept',
            'admin'=>$this->admin,
            'cash'=>$this->cash
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification_type'=>'cash_for_accept',
            'admin'=>$this->admin,
            'cash'=>$this->cash
        ]);
    }
}
