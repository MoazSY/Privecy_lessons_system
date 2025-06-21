<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student_university_stage extends Model
{
    use Notifiable,HasFactory;
    protected $table='student_university_stage';
    protected $fillable=[
        'student_id',
        'university_stage_id'
    ];
}
