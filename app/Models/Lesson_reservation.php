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
    // 'subjectable'
    'subjectable_id',
    'subjectable_type'
    ];
    public function subjectable(){
        return $this->morphTo();
    }

    public function payments()
{
    return $this->morphMany(Payment_transaction::class, 'S_or_G_lesson');
}
public function lesson_session(){
    return $this->morphMany(Lesson_session::class,'S_or_G_lesson');
}
        public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
    public function student(){
        return $this->belongsTo(Students::class);
    }
}
