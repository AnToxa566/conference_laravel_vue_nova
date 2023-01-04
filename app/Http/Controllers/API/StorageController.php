<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    public function downloadExportCsvFile(string $fileName): JsonResponse|BinaryFileResponse
    {
        if (!Storage::disk('exports_csv')->exists($fileName)) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return response()->download(storage_path('app/exports_csv/' . $fileName), $fileName);
    }
}
