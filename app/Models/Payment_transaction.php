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
        // 'S_or_G_lesson',
        'S_or_G_lesson_id',
        'S_or_G_lesson_type',
        'amount',
        'currency',
        // 'descreption',
        'Admin_Id',
        'admin_payout_teacher',
        'teacher_disscount',
        'disscount_percentage',
        'teacher_amount_final',
        'commission',
        'commission_value',
        'payment_transaction_time'
    ];

    // public function S_or_G_lesson()
    // {
    //     return $this->morphTo();
    // }
public function S_or_G_lesson()
{
    return $this->morphTo(__FUNCTION__, 's_or_g_lesson_type', 's_or_g_lesson_id');
}

}
