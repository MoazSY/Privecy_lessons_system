<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sessionStart extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $student;
    public $teacher;
    public $role;
    public $session;
    public function __construct($student,$teacher,$role,$session)
    {
        $this->student=$student;
        $this->teacher=$teacher;
        $this->role=$role;
        $this->session=$session;
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
        if($this->role=='student'){
        return [
            'notification_type'=>'session_start_on_student',
            'teacher'=>$this->teacher,
            'session'=>$this->session,
            'can_join' => now()->between($this->session->start_time, $this->session->end_time),
            'subjectable_type'=>$this->session->S_or_G_lesson_type,
            'subject'=>$this->session->subjectable,
        ];
        }elseif($this->role=='teacher'){
                    return [
            'notification_type'=>'session_start_on_teacher',
            'student'=>$this->student,
            'session'=>$this->session,
            'can_join' => now()->between($this->session->start_time, $this->session->end_time),
            'subjectable_type'=>$this->session->S_or_G_lesson_type,
            'subject'=>$this->session->subjectable,
        ];
        }

    }

    public function toBroadcast($notifiable)
    {


        if($this->role=='student'){

            return new BroadcastMessage([
            'notification_type'=>'session_start_on_student',
            'teacher'=>$this->teacher,
            'session'=>$this->session,
            'can_join' => now()->between($this->session->start_time, $this->session->end_time),
            'subjectable_type'=>$this->session->S_or_G_lesson_type,
            'subject'=>$this->session->subjectable,
        ]);

        }elseif($this->role=='teacher'){
            return new BroadcastMessage([
            'notification_type'=>'session_start_on_teacher',
            'student'=>$this->student,
            'session'=>$this->session,
            'can_join' => now()->between($this->session->start_time, $this->session->end_time),
            'subjectable_type'=>$this->session->S_or_G_lesson_type,
            'subject'=>$this->session->subjectable,
        ]);
        }

    }
}
