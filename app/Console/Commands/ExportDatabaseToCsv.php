<?php

namespace App\Console\Commands;

use App\Services\CsvExportService;
use Illuminate\Console\Command;
use Throwable;

class ExportDatabaseToCsv extends Command
{
    protected $signature = 'database:export-csv';

    protected $description = 'Export application database tables to CSV files';

    public function handle(
        CsvExportService $csvExportService
    ): int {

        $this->info('Starting database CSV export...');

        try {

            $csvExportService->exportAll();

            $this->newLine();

            $this->info(
                'Database exported successfully!'
            );

            $this->info(
                'Files location: storage/app/exports'
            );

            return Command::SUCCESS;

        } catch (Throwable $exception) {

            $this->error(
                'Database CSV export failed!'
            );

            $this->error(
                $exception->getMessage()
            );

            return Command::FAILURE;
        }
    }
}
