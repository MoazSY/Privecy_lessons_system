<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class proccessReservation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $reservation;
    public $teacher;

    public function __construct($teacher,$reservation)
    {
        $this->reservation=$reservation;
        $this->teacher=$teacher;

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

    // /**
    //  * Get the array representation of the notification.
    //  *
    //  * @return array<string, mixed>
    //  */
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }


        public function toDatabase($notifiable)
    {
        return [
            'notification_type'=>'teacher_proccess_reservation',
            'teacher'=>$this->teacher,
            'reservation'=>$this->reservation,
            'state_reservation'=>$this->reservation->state_reservation,
            'subjectable_type'=>$this->reservation->subjectable_type,
            'subject'=>$this->reservation->subjectable,


        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification_type'=>'teacher_proccess_reservation',
            'teacher'=>$this->teacher,
            'reservation'=>$this->reservation,
            'state_reservation'=>$this->reservation->state_reservation,
            'subjectable_type'=>$this->reservation->subjectable_type,
            'subject'=>$this->reservation->subjectable,
        ]);
    }
}
