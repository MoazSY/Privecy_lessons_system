<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class Lesson_session extends Model
{
    use Notifiable,HasFactory;
    protected $table='lesson_session';
    protected $fillable=[
        'teacher_id',
        'student_id',
        // 'subjectable',
           'subjectable_id',
        'subjectable_type',
        'sesstion_url',
        'start_url',
        'teacher_duration_minutes',
        'end_time',
        'start_time',
        // 'S_or_G_lesson',
        'S_or_G_lesson_id',
        'S_or_G_lesson_type',
        'meeting_id',
        'status',
        'recording_path',
        'teacher_join_time',
        'student_join_time'
    ];

        protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'teacher_join_time' => 'datetime',
        'student_join_time' => 'datetime'
    ];

public function S_or_G_lesson()
{
    return $this->morphTo(__FUNCTION__, 'S_or_G_lesson_type', 'S_or_G_lesson_id');
}
        public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
    public function student(){
        return $this->belongsTo(Students::class);
    }
    public function subjectable(){
        return $this->morphTo();
    }

        public function presences()
        {
        return $this->hasMany(\App\Models\LessonSessionPresence::class, 'lesson_session_id');
        }

        public function Report(){
            return $this->hasMany(Report::class,'lesson_session');
        }
        /**
         * جمع مدة وجود المدرس بالدقائق عبر كل الفترات
         */
        public function calculateTeacherDuration(): int
        {
        $presences = $this->presences()->where('role','teacher')->get();
        $total = 0;
        foreach ($presences as $p) {
        $start = $p->joined_at;
        $end   = $p->left_at ?? now();
        if ($start) {
        $total += $start->diffInMinutes($end);
        }
        }
        $this->teacher_duration_minutes = $total;
        $this->save();
        return $total;
        }















        public function getRoomUrlAttribute()
        {
        return $this->sesstion_url;
        }

        public function getRoomNameAttribute()
        {
        return $this->meeting_id;
        }



    public function getCanJoinAttribute()
    {
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);
        // $quarterBefore = $startTime->copy()->subMinutes(15);

        return now()->between($startTime, $endTime);
    }

    public function getTimeRemainingAttribute()
    {
        if ($this->is_active) {
            $remaining = now()->diff($this->end_time);
            return [
                'hours' => $remaining->h,
                'minutes' => $remaining->i,
                'seconds' => $remaining->s,
                'total_minutes' => $remaining->h * 60 + $remaining->i
            ];
        }

        return null;
    }

    // public function getRecordingUrlAttribute()
    // {
    //     if ($this->recording_path) {
    //         $jitsiService = new \App\Services\DailyService();
    //         return $jitsiService->getRecordingUrl($this->recording_path);
    //     }

    //     return null;
    // }

    public function getRecordingUrlAttribute()
{
    return $this->recording_path
        ? asset('storage/' . ltrim($this->recording_path, '/'))
        : null;
}

    // public function calculateTeacherDuration()
    // {
    //     if ($this->teacher_join_time && $this->end_time) {
    //         $duration = $this->teacher_join_time->diffInMinutes($this->end_time);
    //         $this->teacher_duration_minutes = $duration;
    //         $this->save();
    //         return $duration;
    //     }
    //     return 0;
    // }

}
