<?php

declare(strict_types=1);

namespace App\Exports;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

use App\Models\Lecture;

class LecturesByConferenceExport implements FromCollection, ShouldQueue, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    use Exportable;

    protected int $conferenceId;

    public function headings(): array
    {
        return [
            'Title',
            'Start time',
            'End time',
            'Description',
            'Comments count',
        ];
    }

    public function failed(Throwable $exception): JsonResponse
    {
        return response()->json($exception);
    }

    public function __construct(int $conferenceId)
    {
        $this->conferenceId = $conferenceId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): Collection
    {
        $lectures = Lecture::where('conference_id', $this->conferenceId)->select('title', 'date_time_start', 'date_time_end', 'description')->withCount('comments')->get();

        foreach ($lectures as $lecture) {
            $lecture->date_time_start = Carbon::parse($lecture->date_time_start)->format('H:i:s');
            $lecture->date_time_end = Carbon::parse($lecture->date_time_end)->format('H:i:s');
        }

        return $lectures;
    }
}
