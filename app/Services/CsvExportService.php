<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class CsvExportService
{
    private string $exportPath;

    public function __construct()
    {
        $this->exportPath = storage_path('app/exports');

        if (!File::exists($this->exportPath)) {
            File::makeDirectory(
                $this->exportPath,
                0755,
                true
            );
        }
    }

    /**
     * Export all application tables.
     */
    public function exportAll(): void
    {
        $this->exportUsers();
        // $this->exportStudyPlans();
        $this->exportCourses();
        $this->exportTasks();
        // $this->exportNotifications();
    }

    /**
     * Export users table.
     */
    public function exportUsers(): void
    {
        $this->exportTable(
            DB::table('users'),
            'users.csv',
            [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]
        );
    }

    /**
     * Export study_plans table.
     */
    // public function exportStudyPlans(): void
    // {
    //     $this->exportTable(
    //         DB::table('study_plans'),
    //         'study_plans.csv',
    //         [
    //             'id',
    //             'user_id',
    //             'available_hours',
    //             'generated_plan',
    //             'created_at',
    //             'updated_at',
    //         ]
    //     );
    // }

    /**
     * Export courses table.
     */
    public function exportCourses(): void
    {
        $this->exportTable(
            DB::table('courses'),
            'courses.csv',
            [
                'id',
                'user_id',
                'name',
                'instructor',
                'code',
                'created_at',
                'updated_at',
            ]
        );
    }

    /**
     * Export tasks table.
     */
    public function exportTasks(): void
    {
        $this->exportTable(
            DB::table('tasks'),
            'tasks.csv',
            [
                'id',
                'course_id',
                'title',
                'description',
                'deadline',
                'estimated_hours',
                'priority',
                'status',
                'completed_at',
                'created_at',
                'updated_at',
            ]
        );
    }

    /**
     * Export notifications table.
     */
    // public function exportNotifications(): void
    // {
    //     $this->exportTable(
    //         DB::table('notifications'),
    //         'notifications.csv',
    //         [
    //             'id',
    //             'user_id',
    //             'title',
    //             'body',
    //             'is_read',
    //             'created_at',
    //         ]
    //     );
    // }

    /**
     * Export any table to CSV.
     */
    private function exportTable(
        Builder $query,
        string $fileName,
        array $columns
    ): void {

        $filePath = $this->exportPath . '/' . $fileName;

        $file = fopen($filePath, 'w');

        if ($file === false) {
            throw new RuntimeException(
                "Unable to create CSV file: {$fileName}"
            );
        }

        /*
         * Add UTF-8 BOM.
         *
         * This helps Microsoft Excel correctly
         * recognize UTF-8 CSV files.
         */
        fwrite($file, "\xEF\xBB\xBF");

        // CSV header
        fputcsv($file, $columns);

        /*
         * Read the database in chunks instead of
         * loading the complete table into memory.
         */
        $query->chunkById(
            500,
            function ($records) use ($file, $columns) {

                foreach ($records as $record) {

                    $row = [];

                    foreach ($columns as $column) {
                        $row[] = $record->{$column};
                    }

                    fputcsv($file, $row);
                }
            },
            'id'
        );

        fclose($file);
    }
}
