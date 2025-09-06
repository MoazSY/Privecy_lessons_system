<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Report extends Model
{
    protected $table='reports';
    use Notifiable,HasFactory;
    protected $fillable=[
        'admin_id',
        'student_id',
        'lesson_session',
        'type_report',
        'reference_report_path',
        'descreption',
        'time_report',
        'state'
    ];
    public function session(){
        return $this->belongsTo(Lesson_session::class,'lesson_session');
    }
    
    public function Report_proccess(){
        return $this->hasMany(Report_proccess::class,'report_id');
    }
}
