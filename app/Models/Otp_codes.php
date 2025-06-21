<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Otp_codes extends Model
{
    use Notifiable,HasFactory;
    protected $fillable=[
        'phone',
        'codes',
        'expires_at'    
    ];
}
