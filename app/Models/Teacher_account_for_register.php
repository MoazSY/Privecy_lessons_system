<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Teacher_account_for_register extends Model
{
    use HasFactory,Notifiable;
    protected $table= 'teacher_account_for_register';
    protected $fillable=[
        'teacher_id',
        'admin_id',
        'state',
        'cause_of_reject'
    ];
    
}
