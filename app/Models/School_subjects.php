<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class School_subjects extends Model
{
    use Notifiable,HasFactory;
    protected $table='school_subjects';
    protected $fillable=[
        'name_subject',
        'about_subject',
        'subject_cover_image',
        'school_stage_id'
    ];

    public function school_stage(){
        return $this->belongsTo(School_stage::class);
    }
    public function Students(){
        return $this->belongsToMany(Students::class, 'student_subject', 'student_id', 'subject_id');
    }
}
