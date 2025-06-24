<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Teacher_school_stage extends Model
{
    use Notifiable,HasFactory;
    protected $table='teacher_school_stage';
    protected $fillable=[
        'teacher_id',
        'school_stage_id'
    ];
}
