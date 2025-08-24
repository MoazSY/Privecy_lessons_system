<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Lesson_session extends Model
{
    use Notifiable,HasFactory;
    protected $table='lesson_session';
    protected $fillable=[
        'teacher_id',
        'student_id',
        'subjectable',
        'sesstion_url',
        'teacher_time_session',
        'S_or_G_lesson'
    ];
public function S_or_G_lesson()
{
    return $this->morphTo(__FUNCTION__, 's_or_g_lesson_type', 's_or_g_lesson_id');
}
        public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
    public function student(){
        return $this->belongsTo(Students::class);
    }
    public function subjectable(){
        return $this->morphTo();
    }
}
