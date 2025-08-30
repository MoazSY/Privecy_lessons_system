<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use Notifiable,HasApiTokens,HasFactory;

     protected  $table='admin';
    protected $fillable=[
        'firstName',
        'lastName',
        'phoneNumber',
        'email',
        'password',
        'image',
        'birthdate',
        'gender',
        'bankAccount',
        'SuperAdmin',
        "CardValue"
    ];

    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, 'user_table');
    }
    public function TeacherAccount(){
        return $this->belongsToMany(Teacher::class, 'teacher_account_for_register', 'admin_id','teacher_id')->withPivot('state', 'cause_of_reject')->withTimestamps();
    }
    public function Card_charging(){
        return $this->belongsToMany(Students::class,'student_card_charging','admin_id','students_id')->withPivot('card_charging','charging_time')->withTimestamps();
    }
    public function Delivery_cash_teacher(){
        return $this->belongsToMany(Teacher::class,'delivery_cash_teacher','admin_id','teacher_id')->withPivot('cash_value','delivery_time')->withTimestamps();
    }
}
