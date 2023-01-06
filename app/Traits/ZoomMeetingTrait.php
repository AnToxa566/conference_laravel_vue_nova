<?php

declare(strict_types=1);

namespace App\Traits;

use DateTime;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;

use App\Models\Lecture;

trait ZoomMeetingTrait
{
    public string $jwt;
    public array $headers;
    public Client $client;


    public function __construct()
    {
        $this->client = new Client();
        $this->jwt = $this->generateZoomToken();
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->jwt,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }


    public function generateZoomToken(): string
    {
        $key = config('app.zoom_api_key');
        $secret = config('app.zoom_api_secret');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+1 minute'),
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }


    public function toZoomTimeFormat(string $dateTime): string
    {
        return (new DateTime($dateTime))->format('Y-m-d\TH:i:s');
    }


    public function createMeeting(Lecture $lecture): JsonResponse
    {
        $path = 'users/me/meetings';
        $url = config('app.zoom_api_url');

        $duration = Carbon::parse($lecture->date_time_end)->diffInMinutes(Carbon::parse($lecture->date_time_start));

        $body = [
            'headers' => $this->headers,
            'body'    => json_encode([
                'topic'      => $lecture->title,
                'type'       => 2,
                'start_time' => $this->toZoomTimeFormat($lecture->date_time_start),
                'duration'   => $duration,
                'agenda'     => $lecture->description,
                'timezone'   => config('app.timezone'),

                'recurrence' => [
                    "type"              => 1,
                    "repeat_interval"   => 1,
                ],

                'settings'   => [
                    'participant_video' => true,
                    'waiting_room'      => true,
                    'host_video'        => true,
                    'watermark'         => true,

                    'join_before_host'  => false,
                    'mute_upon_entry'   => false,

                    'auto_recording'    => 'cloud',
                    'audio'             => 'voip',
                ],
            ]),
        ];

        $response = $this->client->post($url.$path, $body);
        return response()->json($response->getBody()->getContents());
    }
}
