<?php

declare(strict_types=1);

namespace App\Traits;

use DateTime;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;

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


    protected function generateZoomToken(): string
    {
        $payload = [
            'iss' => config('app.zoom_api_key'),
            'exp' => strtotime('+1 minute'),
        ];

        return JWT::encode($payload, config('app.zoom_api_secret'), 'HS256');
    }


    protected function toZoomTimeFormat(string $dateTime): string
    {
        return (new DateTime($dateTime))->format('Y-m-d\TH:i:s');
    }


    protected function getRequestBody(Lecture $lecture): array
    {
        return [
            'headers' => $this->headers,
            'body'    => json_encode([
                'topic'      => $lecture->title,
                'type'       => 2,
                'start_time' => $lecture->date_time_start->format('Y-m-d\TH:i:s'), // $this->toZoomTimeFormat($lecture->date_time_start),
                'duration'   => Carbon::parse($lecture->date_time_end)->diffInMinutes(Carbon::parse($lecture->date_time_start)),
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
    }


    public function createMeeting(Lecture $lecture): array
    {
        return json_decode(
            $this->client->post(
                config('app.zoom_api_url').'users/me/meetings',
                $this->getRequestBody($lecture)
            )->getBody()->getContents(),
            true
        );
    }


    public function getMeeting(int $id): array
    {
        $body = [
            'headers' => $this->headers,
            'body'    => json_encode([]),
        ];

        return json_decode(
            $this->client->get(
                config('app.zoom_api_url').'meetings/'.$id,
                $body
            )->getBody()->getContents(),
            true
        );
    }


    public function getMeetingsPage(string $nextPageToken = ''): array
    {
        $path = 'users/me/meetings?page_size=300&next_page_token='.$nextPageToken;

        $body = [
            'headers' => $this->headers,
            'body'    => json_encode([]),
        ];

        return json_decode($this->client->get(config('app.zoom_api_url').$path, $body)->getBody()->getContents(), true);
    }


    public function getMeetings(): array
    {
        $meetings = [];
        $nextPageToken = "";

        do {
            $response = $this->getMeetingsPage($nextPageToken);
            $nextPageToken = $response['next_page_token'];

            $meetings = array_merge($meetings, $response['meetings']);
        } while ($nextPageToken);

        return $meetings;
    }


    public function updateMeeting(int $id, Lecture $lecture): array
    {
        $this->client->patch(
            config('app.zoom_api_url').'meetings/'.$id,
            $this->getRequestBody($lecture)
        );

        return $this->getMeeting($id);
    }
}
