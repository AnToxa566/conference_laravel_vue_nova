<?php

declare(strict_types=1);

namespace App\Exports;

use Throwable;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\Conference;

class AllConferencesExport extends BaseCsvExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'Title',
            'Start date and time',
            'Latitude',
            'Longitude',
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
        return Conference::select('title', 'date_time_event', 'latitude', 'longitude', 'country')->withCount('lectures', 'listeners')->get();
    }
}
