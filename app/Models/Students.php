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
        "about_him"
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
}
