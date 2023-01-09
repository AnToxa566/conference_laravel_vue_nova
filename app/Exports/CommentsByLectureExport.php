<?php

declare(strict_types=1);

namespace App\Exports;

use Throwable;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Lecture;

class CommentsByLectureExport extends BaseCsvExport implements WithHeadings, FromCollection
{
    /*
    * The lecture's id
    *
    * @var int
    */
    protected int $lectureId;

    public function headings(): array
    {
        return [
            'User first name',
            'User last name',
            'Published date',
            'Content',
        ];
    }

    public function failed(Throwable $exception): JsonResponse
    {
        return response()->json($exception);
    }

    public function __construct(int $lectureId)
    {
        $this->lectureId = $lectureId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): Collection
    {
        return Lecture::find($this->lectureId)->comments()
                        ->leftJoin('users', 'users.id', '=', 'comments.user_id')
                        ->select('users.first_name', 'users.last_name', 'comments.created_at', 'comments.description')
                        ->get();
    }
}
