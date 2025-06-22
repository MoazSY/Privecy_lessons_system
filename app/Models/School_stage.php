<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class School_stage extends Model
{
    use Notifiable,HasFactory;
    protected $table='school_stage';
    protected $fillable=[
        'className',
        'school_stage',
        'semester'
    ];

    public function School_subjects(){
        return $this->hasMany(School_subjects::class);
    }
    public function Student(){
        return $this->belongsToMany(Students::class);
    }
}
