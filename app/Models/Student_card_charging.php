<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Student_card_charging extends Model
{
    use Notifiable,HasFactory;
   protected $table='student_card_charging';
   protected $fillable=[
   'admin_id',
   'students_id',
    'card_charging',
    'charging_time'
   ];
}
