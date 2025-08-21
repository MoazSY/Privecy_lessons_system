<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Payment_transaction extends Model
{
    use Notifiable,HasFactory;
    protected $table='payment_transaction';
    protected  $fillable=[
        'teacher_id',
        'student_id',
        'S_or_G_lesson',
        'amount',
        'currency',
        'descreption',
        'admin_payout_teacher',
        'teacher_disscount',
        'disscount_percentage',
        'teacher_amount_final'
    ];

    public function S_or_G_lesson()
    {
        return $this->morphTo();
    }

}
