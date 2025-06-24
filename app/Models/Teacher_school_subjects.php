<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Teacher_school_subjects extends Model
{
    use Notifiable,HasFactory;
    protected $table= 'teacher_school_subjects';
    protected $fillable=[
        'teacher_id',
        'school_subject_id',
        'lesson_duration',
        'lesson_price'
    ];
}
