<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;
use App\Services\CsvExportService;
use Illuminate\Support\Facades\File;
class DataExportController extends Controller
{
    public function users(): StreamedResponse
    {
        return $this->downloadCsv('users.csv');
    }

    public function courses(): StreamedResponse
    {
        return $this->downloadCsv('courses.csv');
    }

    // public function studyPlans(): StreamedResponse
    // {
    //     return $this->downloadCsv('study_plans.csv');
    // }

    public function tasks(): StreamedResponse
    {
        return $this->downloadCsv('tasks.csv');
    }

    // public function notifications(): StreamedResponse
    // {
    //     return $this->downloadCsv('notifications.csv');
    // }

    private function downloadCsv(string $fileName): StreamedResponse
    {
        $path = storage_path('app/exports/' . $fileName);

        if (!file_exists($path)) {
            abort(404, 'CSV file not found.');
        }

        return response()->streamDownload(
            function () use ($path) {
                readfile($path);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }


    // Download all CSV files as a ZIP
    public function downloadAll(CsvExportService $csvExportService)
    {
        // Generate fresh CSV files
        $csvExportService->exportAll();

        $exportPath = storage_path('app/exports');
        $zipPath = storage_path('app/planora_dataset.zip');

        // Delete old zip if exists
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return response()->json([
                'message' => 'Unable to create ZIP file'
            ], 500);
        }

        foreach (File::files($exportPath) as $file) {
            $zip->addFile(
                $file->getRealPath(),
                $file->getFilename()
            );
        }

        $zip->close();

        return response()->download(
            $zipPath,
            'planora_dataset.zip'
        )->deleteFileAfterSend();
    }
}
