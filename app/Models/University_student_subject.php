<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class University_student_subject extends Model
{
    use Notifiable,HasFactory;
    protected $table='university_student_subject';
    protected $fillable=[
        'student_id',
        'university_subject'
    ];
}
