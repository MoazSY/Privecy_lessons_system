<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Teacher_available_worktime extends Model
{
    use Notifiable,HasFactory;
    protected $table= 'teacher_available_worktime';
    protected $fillable=[
        'teacher_id',
        'workingDay',
        'start_time',
        'end_time',
        'break_duration_lessons'
    ];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
}
