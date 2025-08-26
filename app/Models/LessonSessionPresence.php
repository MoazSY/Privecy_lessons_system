<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonSessionPresence extends Model
{
    protected $fillable = [
        'lesson_session_id','user_id','role','joined_at','left_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at'   => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(Lesson_session::class, 'lesson_session_id');
    }
}
