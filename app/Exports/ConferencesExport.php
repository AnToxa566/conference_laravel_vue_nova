<?php

declare(strict_types=1);

namespace App\Exports;

use Throwable;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

use App\Models\Conference;
use App\Http\Controllers\API\ConferenceController;

class ConferencesExport implements FromCollection, ShouldQueue, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Title',
            'Start date and time',
            'Country',
            'Lectures count',
            'Listeners count',
        ];
    }

    public function failed(Throwable $exception): JsonResponse
    {
        return response()->json($exception);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): Collection
    {
        // dd((new ConferenceController)->getConferenceAddressById(24));

        return Conference::select('title', 'date_time_event', 'country')->withCount('lectures', 'listeners')->get();
    }
}
