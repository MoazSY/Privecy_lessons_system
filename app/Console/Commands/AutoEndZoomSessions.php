<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lesson_session;
use App\Services\ZoomService;

class AutoEndZoomSessions extends Command
{
    protected $signature = 'zoom:sessions:auto-end';
    protected $description = 'End active Zoom meetings that reached their end_time';

    public function handle()
    {
        $zoom = new ZoomService();

        $sessions = Lesson_session::where('status','active')
            ->where('end_time','<=', now())
            ->get();

        foreach ($sessions as $s) {
            try {

                $s->presences()->whereNull('left_at')->update(['left_at'=>now()]);


                $minutes = $s->calculateTeacherDuration();

         
                try { $zoom->endMeeting($s->meeting_id); } catch (\Throwable $e) {}

                $s->update([
                    'status' => 'completed',
                    'teacher_duration_minutes' => $minutes,
                ]);

                $this->info("Ended session {$s->id} meeting {$s->meeting_id}");
            } catch (\Throwable $e) {
                $this->error("End failed for session {$s->id}: ".$e->getMessage());
            }
        }

        $this->info('Auto end done.');
    }
}
