<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ZoomService
{
    protected string $baseUrl;
    protected string $accountId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $userId;

    public function __construct()
    {
        $this->baseUrl      = config('services.zoom.base_url');
        $this->accountId    = config('services.zoom.account_id');
        $this->clientId     = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
        $this->userId       = config('services.zoom.user_id', 'me');
    }

    protected function token(): string
    {
        return Cache::remember('zoom_access_token', 50 * 60, function () {
            $res = Http::asForm()->withBasicAuth($this->clientId, $this->clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if (!$res->successful()) {
                throw new \RuntimeException('Zoom token error: ' . $res->body());
            }

            return $res->json('access_token');
        });
    }

    protected function client()
    {
        return Http::withToken($this->token())
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->retry(2, 500);
    }


    public function createMeeting(string $topic, string $startTime, int $duration): array
    {

        $startUTC = Carbon::parse($startTime)->toIso8601String();

        $payload = [
            'topic'      => $topic,
            'type'       => 2,
            'start_time' => $startUTC,
            'duration'   => $duration,
            'settings'   => [
                'join_before_host' => true,
                'waiting_room'     => false,
                'mute_upon_entry'  => false,
                'host_video'       => true,
                'participant_video'=> true,
                'allow_multiple_devices'=> true,
            ],
        ];

        $res = $this->client()->post("/users/{$this->userId}/meetings", $payload);
        if (!$res->successful()) {
            throw new \RuntimeException('Zoom create meeting failed: ' . $res->body());
        }

        $data = $res->json();

        return [
            'meeting_id' => (string)$data['id'],
            'join_url'   => $data['join_url'],   // للطالب
            'start_url'  => $data['start_url'],  // للمدرّس (مستضيف)
            'start_time' => $startUTC,
            'duration'   => $duration,
        ];
    }


    public function endMeeting(string $meetingId): void
    {
        $res = $this->client()->put("/meetings/{$meetingId}/status", [
            'action' => 'end',
        ]);
        if (!$res->successful()) {

        }
    }
}
