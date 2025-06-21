<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class University_stage extends Model
{
    use Notifiable,HasFactory;
    protected $table='university_stage';
    protected $fillable=[
        'university_type',// حكومي او خاص
        'university_branch', // اسم الفرع الجامعي دمشق و حلب و قلمون ووو
        'college_name',// هندسة معلوماتية طب بشري هندسة طاقة
        'study_year',// السنة الدراسية اولى ثانية .....
        'specialize',// هذا بوليان  يوجد تخصص في هذه السنة ام لا
        'specialize_name',// اسم التخصص  مثال السنة الرابعة معلوماتية فيها ثلاث تخصصات( برميات,شبكات,ذكاء)
        'semester'// فصل اول ثاني
    ];
    public function University_subjects(){
        return $this->hasMany(University_subjects::class);
    }
    public function Students(){
        return $this->belongsToMany(Students::class);
    }
}
