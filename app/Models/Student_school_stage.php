<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student_school_stage extends Model
{
    use Notifiable,HasFactory;

    protected $table='student_school_stage';
    protected $fillable=[
        'student_id',
        'school_stage_id'
    ];

}
