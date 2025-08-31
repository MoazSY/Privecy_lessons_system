<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lesson_reservation;
use App\Models\Lesson_session;
use App\Services\ZoomService;
use Carbon\Carbon;

class AutoCreateZoomSessions extends Command
{
    protected $signature = 'zoom:sessions:auto-create';
    protected $description = 'Create Zoom meetings 15 minutes before reservation time';

    public function handle()
    {
        $zoom = new ZoomService();
        $now  = now();

        // $reservations = Lesson_reservation::where('state_reservation','accepted')
        //     ->whereBetween('reservation_time', [
        //         $now->copy()->addMinutes(15),
        //         $now->copy()->addMinutes(16),
        //     ])
        //     ->whereDoesntHave('lesson_session')
        //     ->get();

        $reservations = Lesson_reservation::where('state_reservation', 'accepted')
        ->where('reservation_time', '>=', $now)                    
        ->where('reservation_time', '<=', $now->copy()->addMinutes(15)) 
        ->whereDoesntHave('lesson_session')
        ->get();

        foreach ($reservations as $r) {
            try {
                $start  = Carbon::parse($r->reservation_time);
                $end    = $start->copy()->addMinutes($r->duration);

                $topic = 'Lesson ' . ($r->subjectable->name_subject ?? 'Subject') .
                         ' - ' . ($r->teacher->firstName ?? 'Teacher');

                $m = $zoom->createMeeting($topic, $start->toIso8601String(), (int)$r->duration);

                Lesson_session::create([
                    'teacher_id'        => $r->teacher_id,
                    'student_id'        => $r->student_id,
                    'subjectable_id'    => $r->subjectable_id,
                    'subjectable_type'  => $r->subjectable_type,
                    'sesstion_url'      => $m['join_url'],
                    'start_url'         => $m['start_url'],
                    'start_time'        => $start,
                    'end_time'          => $end,
                    'S_or_G_lesson_id'  => $r->id,
                    'S_or_G_lesson_type'=> Lesson_reservation::class,
                    'meeting_id'        => $m['meeting_id'],
                    'status'            => 'scheduled',
                ]);

                $this->info("Created Zoom session for reservation {$r->id} (meeting {$m['meeting_id']})");
            } catch (\Throwable $e) {
                $this->error("Failed reservation {$r->id}: ".$e->getMessage());
            }
        }

        $this->info('Done. Created '.$reservations->count().' sessions.');
    }
}
