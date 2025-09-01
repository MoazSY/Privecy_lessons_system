<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class teacherProfileProccess extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $admin;
    public $proccess_profile;
    public function __construct($admin,$proccess_profile)
    {
        $this->admin=$admin;
        $this->proccess_profile=$proccess_profile;
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
            'notification_type'=>'admin_proccess_profile',
            'admin'=>$this->admin,
            'status_profile'=>$this->proccess_profile['status'],
            'reject_cause'=>$this->proccess_profile['reject_cause']
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification_type'=>'admin_proccess_profile',
            'admin'=>$this->admin,
            'status_profile'=>$this->proccess_profile['status'],
            'reject_cause'=>$this->proccess_profile['reject_cause']
        ]);
    }
}
