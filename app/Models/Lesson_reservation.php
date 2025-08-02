<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;

class Lesson_reservation extends Model
{
    use Notifiable,HasFactory;
    protected $table='lesson_reservation';
    protected $fillable=[
'teacher_id',
'student_id',
'reservation_time',
'duration',
'reservation_day',
'state_reservation',
'subjectable'
    ];
    public function subject_table(){
        return $this->morphTo();
    }
}
