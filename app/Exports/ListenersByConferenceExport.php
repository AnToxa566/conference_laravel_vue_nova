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

class ListenersByConferenceExport implements FromCollection, ShouldQueue, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    use Exportable;

    protected int $conferenceId;

    public function headings(): array
    {
        return [
            'First name',
            'Last name',
            'Birthdate',
            'Country',
            'Phone',
            'Email',
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
        return Conference::find($this->conferenceId)->listeners()->select('first_name', 'last_name', 'birthdate', 'country', 'phone_number', 'email')->get();
    }
}
