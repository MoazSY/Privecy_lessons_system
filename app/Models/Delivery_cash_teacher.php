<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Delivery_cash_teacher extends Model
{
    use Notifiable,HasFactory;

    protected $table='delivery_cash_teacher';
    protected $fillable=[
        'admin_id',
        'teacher_id',
        'cash_value',
        'delivery_time',
        'teacher_acceptance'
    ];
}
