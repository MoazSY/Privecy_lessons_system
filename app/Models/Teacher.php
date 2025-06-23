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
        'Activate_Account'
    ];

    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, 'user_table');
    }

}
