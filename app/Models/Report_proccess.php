<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Report_proccess extends Model
{
    use Notifiable,HasFactory;
    protected $table='proccess_report';
    protected $fillable=[
        'admin_id',
        'report_id',
        'proccess_method',
        'block_type',
        'block_duaration_value',
        'disscount_percentage_value',
        'response_time'
    ];
    
}
