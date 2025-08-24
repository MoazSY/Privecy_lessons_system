<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class University_subjects extends Model
{
    use Notifiable,HasFactory;
    protected $table='university_subjects';
    protected $fillable=[
        'university_stage_id',
        'subject_name',
        'about_subject',
        'subject_cover_image'
    ];
    public function University_stage(){
        return $this->belongsTo(University_stage::class);
    }
    public function Students(){
        return $this->belongsToMany(Students::class);
    }
    public function Teachers(){
        return $this->belongsToMany(Teacher::class, 'teacher_university_stage', 'teacher_id', 'university_subjects_id')->withPivot('lesson_duration', 'lesson_price')->withTimestamps();
    }

        public function reservations(){
        return $this->morphMany(Lesson_reservation::class,'subjectable');
    }
    public function lesson_session(){
    return $this->morphMany(Lesson_session::class,'subjectable');
    }
}
