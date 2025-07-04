<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Teacher_university_stage extends Model
{
    use Notifiable,HasFactory;
    protected $table='teacher_university_stage';
    protected $fillable=[
        'teacher_id',
        'university_stage_id'
    ];
}
