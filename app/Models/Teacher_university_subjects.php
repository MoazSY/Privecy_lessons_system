<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Teacher_university_subjects extends Model
{
    use Notifiable,HasFactory;
    protected $table= 'teacher_university_subjects';
    protected $fiilable=[
        'teacher_id',
        'university_subjects_id',
        'lesson_duration',
        'lesson_price'
    ];
}
