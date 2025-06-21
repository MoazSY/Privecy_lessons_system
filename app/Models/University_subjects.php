<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class University_subjects extends Model
{
    use Notifiable,HasFactory;
    protected $table='university_subjects';
    protected $fillable=[
        'university_stage_id',
        'subject_name',
        'about_subject',
        'subject_cover_image'
    ];
    public function University_stage(){
        return $this->belongsTo(University_stage::class);
    }
    public function Students(){
        return $this->belongsToMany(Students::class);
    }
}
