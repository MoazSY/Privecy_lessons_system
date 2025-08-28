<?php

namespace App\Http\Controllers;

use App\Models\Lesson_session;
use App\Models\Lesson_reservation;
use App\Models\LessonSessionPresence;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

class ZoomSessionController extends Controller
{
    public function __construct(protected ZoomService $zoom) {}


    public function autoCreateSession($reservationId)
    {
        $reservation = Lesson_reservation::with(['teacher','student','subjectable'])
            ->findOrFail($reservationId);

        $reservationTime = Carbon::parse($reservation->reservation_time);
        $createTime      = $reservationTime->copy()->subMinutes(15);

        if (now()->lt($createTime)||now()->gt($reservationTime)) {
            return response()->json([
                'success' => false,
                'message' => 'session will create before 15 minute from start',
            ], 400);
        }


        $existing = Lesson_session::where('S_or_G_lesson_id',$reservation->id)
            ->where('S_or_G_lesson_type', Lesson_reservation::class)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'sesstion pre existing',
                'data'    => $existing
            ]);
        }

        $endTime = $reservationTime->copy()->addMinutes($reservation->duration);

        $topic = 'Lesson ' . ($reservation->subjectable->name_subject ?? 'Subject') .
                 ' - ' . ($reservation->teacher->firstName ?? 'Teacher');

        $meeting = $this->zoom->createMeeting(
            topic: $topic,
            startTime: $reservationTime->toIso8601String(),
            duration: (int)$reservation->duration
        );

        $session = Lesson_session::create([
            'teacher_id'        => $reservation->teacher_id,
            'student_id'        => $reservation->student_id,
            'subjectable_id'    => $reservation->subjectable_id,
            'subjectable_type'  => $reservation->subjectable_type,
            'sesstion_url'      => $meeting['join_url'],  // للطالب
            'start_url'         => $meeting['start_url'],
            'start_time'        => $reservationTime,
            'end_time'          => $endTime,
            'S_or_G_lesson_id'  => $reservation->id,
            'S_or_G_lesson_type'=> Lesson_reservation::class,
            'meeting_id'        => $meeting['meeting_id'],
            'status'            => 'scheduled',

        ]);

        return response()->json([
            'success' => true,
            'message' => 'sesstion created successfully',
            'data'    => [
                'session'    => $session,
                'join_url'   => $meeting['join_url'],
                'start_url'  => $meeting['start_url'], // أعرضه هنا فقط، لا تخزّنه إن مو حاب
            ]
        ]);
    }

    /**
     */
    public function joinAsTeacher($sessionId)
    {
        $session = Lesson_session::with(['teacher','student'])->findOrFail($sessionId);

        if (!now()->between($session->start_time, $session->end_time)) {
            return response()->json([
                'success' => false,
                'message' => 'you dont join sesstion before start session or after end '
            ], 400);
        }
        $teacher_id=Auth::guard('teacher')->user()->id;

        if($teacher_id!=$session->teacher_id){
                 return response()->json([
                'success' => false,
                'message' => 'teacher cant join to this session'
            ], 400);
        }

        $startUrl = $session->start_url;

        LessonSessionPresence::create([
            'lesson_session_id' => $session->id,
            'user_id'           => $session->teacher_id,
            'role'              => 'teacher',
            'joined_at'         => now(),
        ]);

        $session->update([
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'join_url' => $startUrl,
                'session'  => $session,
                'user_type'=> 'teacher',
            ]
        ]);
    }

    public function joinAsStudent($sessionId)
    {
        $session = Lesson_session::with(['student'])->findOrFail($sessionId);

        if(Auth::guard('student')->user()->id!=$session->student_id){
                return response()->json([
                'success' => false,
                'message' => 'student cant join to this session'
            ], 400);
        }
        if (!now()->between($session->start_time, $session->end_time)) {
            return response()->json([
                'success' => false,
                'message' => 'you dont join sesstion before start session or after end '
            ], 400);
        }


        LessonSessionPresence::create([
            'lesson_session_id' => $session->id,
            'user_id'           => $session->student_id,
            'role'              => 'student',
            'joined_at'         => now(),
        ]);

        $session->update([
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'join_url' => $session->sesstion_url,
                'session'  => $session,
                'user_type'=> 'student',
            ]
        ]);
    }


    public function endSession(Request $request, $sessionId)
    {
        $request->validate([
            'recording_file' => 'sometimes|file|mimes:mp4,mov,mkv,webm,avi|max:512000', // حتى 500MB مثالاً
        ]);

        $session = Lesson_session::with('presences')->findOrFail($sessionId);


        $session->presences()->whereNull('left_at')->update(['left_at' => now()]);


        $teacherMinutes = $session->calculateTeacherDuration();

        try {
            $this->zoom->endMeeting($session->meeting_id);
        } catch (\Throwable $e) {}

        $updates = [
            'status'   => 'completed',
            'end_time' => now(),
            'teacher_duration_minutes' => $teacherMinutes,
        ];

        if ($request->hasFile('recording_file')) {
            $path = $request->file('recording_file')->store('recordings', 'public');
            $updates['recording_path'] = $path;
        }

        $session->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'session ended successfully',
            'recording_url' => $session->recording_path ? asset('storage/'.$session->recording_path) : null,
            'teacher_duration_minutes' => $teacherMinutes,
        ]);
    }

    public function leave($sessionId,Request $request)
    {


        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        // $student=Auth::guard('student')->user()->id;
        if ($user instanceof \App\Models\Students) {
            $user_id=$user->id;
            $role='student';
        } elseif ($user instanceof \App\Models\Teacher) {
            $user_id=$user->id;
            $role='teacher';
        }

        $presence = LessonSessionPresence::where('lesson_session_id',$sessionId)
            ->where('user_id',$user_id)
            ->where('role',$role)
            ->whereNull('left_at')
            ->latest()
            ->first();

        if ($presence) {
            $presence->update(['left_at' => now()]);
        }

        return response()->json(['success'=>true]);
    }

    public function getSessionInfo($sessionId)
    {
        $session = Lesson_session::with(['teacher','student','subjectable','presences'])->findOrFail($sessionId);
        return response()->json([
            'success' => true,
            'data' => [
                'session' => $session,
                'can_join' => now()->between($session->start_time, $session->end_time),
                'status' => $session->status,
                'recording_url' => $session->recording_path ? asset('storage/'.$session->recording_path) : null,
            ]
        ]);
    }
    public function get_session(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        if ($user instanceof \App\Models\Students) {
         $lesson_session=$user->lesson_session()->with(['teacher','subjectable'])->orderBy('start_time','asc')->get();
         $role='student';
        }
         elseif ($user instanceof \App\Models\Teacher) {
         $lesson_session=$user->lesson_session()->with(['student','subjectable'])->orderBy('start_time','asc')->get();
         $role='teacher';

        }
   $lesson_session ->map(function ($lesson_session)use ($role) {
    $currentDateTime = Carbon::now();

    $reservationDateTime = Carbon::parse($lesson_session->start_time);

    $timeDifference = $currentDateTime->diff($reservationDateTime);

    $lesson_session->time_remaining = [
        'days' => $timeDifference->d,
        'hours' => $timeDifference->h,
        'minutes' => $timeDifference->i,
        'total_hours' => $timeDifference->h + ($timeDifference->d * 24)
    ];
    $lesson_session->can_join= now()->between($lesson_session->start_time, $lesson_session->end_time);
    $lesson_session->recording_url_session=$lesson_session->recording_path ? asset('storage/'.$lesson_session->recording_path) : null;
    $lesson_session->is_past = $currentDateTime->greaterThan($reservationDateTime);
    $lesson_session->is_upcoming = !$lesson_session->is_past;
    $lesson_session->human_readable = $timeDifference->format('%d day, %h hour, %i minute');
    $lesson_session->teacherDuration=$lesson_session->teacher_duration_minutes;
    if($role=='teacher'){
        $haspaid = optional(
        $lesson_session->S_or_G_lesson?->payments()->latest()->first()
        )->admin_payout_teacher;
        $teacherGain = optional(
        $lesson_session->S_or_G_lesson?->payments()->latest()->first()
        )->teacher_amount_final;
        $lesson_session->adminPay=$haspaid;
        $lesson_session->teacherGain=$teacherGain;
    }
    return $lesson_session;
});

return response()->json(['message'=>'all sessions related to user','sessions'=>$lesson_session]);
    }
}
