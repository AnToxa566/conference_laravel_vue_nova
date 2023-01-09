<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelTypes;
use App\Exports\BaseCsvExport;

class ExportFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /*
    * The file's name
    *
    * @var string
    */
    public string $fileName;

    /*
    * Exportable instance
    *
    * @var BaseCsvExport
    */
    public BaseCsvExport $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $fileName, BaseCsvExport $export)
    {
        $this->fileName = $fileName;
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (Storage::disk('exports_csv')->exists($this->fileName)) {
            ExportCompleted::dispatch($this->fileName)->delay(now()->addSeconds(5));
            return;
        }

        $this->export->store($this->fileName, 'exports_csv', ExcelTypes::CSV)->chain([
            new ExportCompleted($this->fileName),
        ])->delay(now()->addSeconds(5));

        DeleteFile::dispatch($this->fileName)->delay(now()->addMinutes(15));
    }
}
