<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Teacher_rating extends Model
{
  use Notifiable,HasFactory;
  protected $table='teacher_rating';
  protected $fillable=[
    'student_id',
    'teacher_id',
    'rate'
  ];
  
}
