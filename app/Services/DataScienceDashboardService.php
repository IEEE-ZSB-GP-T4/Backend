<?php

namespace App\Services;

use RuntimeException;
use Symfony\Component\Process\Process;

class DataScienceDashboardService
{
    public function __construct(
        private readonly CsvExportService $csvExportService
    ) {}

    public function generateForUser(int $userId): array
    {
        $this->csvExportService->exportAll();

        $scriptPath = base_path('Data-Science/src/connect.py');
        $scriptDirectory = dirname($scriptPath);

        if (! file_exists($scriptPath)) {
            throw new RuntimeException('Data science dashboard script was not found.');
        }

        $pythonPath = $this->resolvePythonPath();

        $process = new Process(
            [
                $pythonPath,
                $scriptPath,
                (string) $userId,
            ],
            $scriptDirectory,
            [
                'DATA_DIR' => storage_path('app/exports'),
            ]
        );

        $process->setTimeout((int) config('services.data_science.timeout', 60));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(
                trim($process->getErrorOutput())
                    ?: "Data science dashboard generation failed using {$pythonPath}."
            );
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload)) {
            throw new RuntimeException('Data science dashboard returned invalid JSON.');
        }

        if (($payload['status'] ?? null) === 'error') {
            throw new RuntimeException($payload['message'] ?? 'Data science dashboard returned an error.');
        }

        return $payload;
    }

    private function resolvePythonPath(): string
    {
        $configuredPath = (string) config('services.data_science.python', 'python3');
        $dockerVenvPath = '/opt/data-science-venv/bin/python';

        if (file_exists($dockerVenvPath)) {
            return $dockerVenvPath;
        }

        return $configuredPath;
    }
}
