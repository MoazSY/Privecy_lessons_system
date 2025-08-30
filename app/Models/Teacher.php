<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Authenticatable
{
    use Notifiable,HasApiTokens,HasFactory;
    protected $table='teacher';
    protected $fillable=[
        'firstName',
        'lastName',
        'image',
        'identification_image',
        'birthdate',
        'phoneNumber',
        'url_certificate_file',
        'about_teacher',
        'email',
        'password',
        'gender',
        'account_number',
        'Activate_Account',
        "CardValue"
    ];

    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, 'user_table');
    }
    public function University_subjects(){
        return $this->belongsToMany(University_subjects::class, 'teacher_university_subjects', 'teacher_id', 'university_subjects_id')->withPivot('lesson_duration', 'lesson_price')->withTimestamps();
    }
    public function School_subjects()
    {
    return $this->belongsToMany(School_subjects::class, 'teacher_school_subjects', 'teacher_id', 'school_subject_id')->withPivot('lesson_duration', 'lesson_price')->withTimestamps();
    }
    public function School_stage(){
    return $this->belongsToMany(School_stage::class, 'teacher_school_stage', 'teacher_id', 'school_stage_id');
    }
    public function University_stage(){
        return $this->belongsToMany(University_stage::class, 'teacher_university_stage', 'teacher_id', 'university_stage_id');
    }
    public function available_worktime(){
        return $this->hasMany(Teacher_available_worktime::class);
    }

     public function Rating(){
        return $this->hasMany(Teacher_rating::class,'teacher_id');
     }

    public function folowing_value(){
        return $this->hasMany(Teacher_following::class,'teacher_id');
    }

    public function Reservations(){
        return $this->hasMany(Lesson_reservation::class,'teacher_id');
    }
    public function lesson_session(){
    return $this->hasMany(Lesson_session::class,'teacher_id');
    }
    public function Delivery_cash_teacher(){
        return $this->hasMany(Delivery_cash_teacher::class,'teacher_id');
    }
}

