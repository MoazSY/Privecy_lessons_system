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
        'bankAccount'
    ];

    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, 'user_table');
    }

    public function TeacherAccount(){
        return $this->belongsToMany(Teacher::class, 'teacher_account_for_register', 'admin_id','teacher_id')->withPivot('state', 'cause_of_reject')->withTimestamps();
    }
}
