<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LessonBooked extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $data;
    public $student;
    public $subject;

    public function __construct($data,$student,$subject)
    {
        $this->data = $data;
        $this->student=$student;
        $this->subject=$subject;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
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
            'notification_type'=>'student_booked_lesson',
            'student_firstname'=>$this->student->firstName,
            'student_lastname'=>$this->student->lastName,
            'student_url'=>asset('storage/'.$this->student->image),
            'reservation_time'=>$this->data->reservation_time,
            'reservation_day'=>$this->data->reservation_day,
            'subjectable_type'=>$this->data->subjectable_type,
            'subject'=>$this->data->subjectable


        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification_type'=>'student_booked_lesson',
            'student_firstname'=>$this->student->firstName,
            'student_lastname'=>$this->student->lastName,
            'student_url'=>asset('storage/'.$this->student->image),
            'reservation_time'=>$this->data->reservation_time,
            'reservation_day'=>$this->data->reservation_day,
            'subjectable_type'=>$this->data->subjectable_type,
            'subject'=>$this->data->subjectable
        ]);
    }

}
