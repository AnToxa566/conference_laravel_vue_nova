<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class BaseCsvExport implements ShouldQueue, ShouldAutoSize, WithStrictNullComparison
{
    use Exportable;
}
