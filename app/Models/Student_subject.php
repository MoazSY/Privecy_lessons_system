<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student_subject extends Model
{
    use Notifiable,HasFactory;
    protected $table='student_subject';
    protected $fillable=[
        'student_id',
        'subject_id'
    ];
}
