<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher_following extends Model
{
    use Notifiable,HasFactory;
    protected $table='teacher_following';
    protected $fillable=[
        'student_id',
        'teacher_id',
        'following_state',
        'recieve_notifications'
    ];

}
