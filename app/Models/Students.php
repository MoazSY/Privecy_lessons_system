<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Students extends Authenticatable
{
    use Notifiable,HasFactory,HasApiTokens;
    protected $table='students';
     protected $fillable=[
        "firstName",
        "lastName",
        "birthdate",
        "image",
        "email",
        "password",
        "idintification_image",
        "phoneNumber",
        "gender",
        "accountNumber",
        "is_profile_completed ",
        "about_him",
        "CardValue"
     ];
     protected $hidden=[
        "password",
        "remember_token"
     ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, 'user_table');
    }
    public function School_stage(){
        return $this->belongsToMany(School_stage::class, 'student_school_stage', 'student_id', 'school_stage_id');
    }
    public function Subjects(){
        return $this->belongsToMany(School_subjects::class, 'student_subject', 'student_id', 'subject_id');
    }
    public function University_stage(){
        return $this->belongsToMany(University_stage::class, 'student_university_stage', 'student_id', 'university_stage_id');
    }
    public function Univesity_subjects(){
        return $this->belongsToMany(University_subjects::class, 'university_student_subject', 'student_id', 'university_subject');
    }
    public function Rating(){
        return $this->belongsToMany(Teacher::class,'teacher_rating','student_id','teacher_id')->withPivot("rate")->withTimestamps();
    }
    public function Following(){
        return $this->belongsToMany(Teacher::class,'teacher_following','student_id','teacher_id')->withPivot('following_state','recieve_notifications')->withTimestamps();
    }
     public function Reservations(){
        return $this->hasMany(Lesson_reservation::class,'student_id');
    }
    public function card_charging(){
        return $this->hasMany(Student_card_charging::class);
    }
}
