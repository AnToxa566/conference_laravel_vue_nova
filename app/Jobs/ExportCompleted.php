<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\FileExportCompleted;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /*
    * The file's name
    *
    * @var string
    */
    public $fileName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        FileExportCompleted::dispatch($this->fileName);
    }
}
