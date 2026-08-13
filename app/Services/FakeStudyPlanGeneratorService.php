<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FakeStudyPlanGeneratorService
{
    public function generate(float $availableHours, Collection $tasks): array
{
    // Sort tasks by deadline ascending, then by priority descending
    $tasks = $tasks->sortBy([
        ['deadline', 'asc'],
        fn ($a, $b) => $this->priorityWeight($b->priority) <=> $this->priorityWeight($a->priority),
    ])->values();

   // Initialize remaining hours for each task
    $remaining = [];
    foreach ($tasks as $task) {
        $remaining[$task->id] = [
            'task_id'  => $task->id,
            'title'    => $task->title,
            'deadline' => Carbon::parse($task->deadline)->startOfDay(),
            'hours'    => (float) $task->estimated_hours,
        ];
    }

    if (empty($remaining)) {
        return ['days' => [], 'warnings' => []];
    }

    $lastDeadline = collect($remaining)->max(fn ($t) => $t['deadline']);
    $date = Carbon::today();

    $days = [];

    while ($date->lte($lastDeadline)) {
        $hoursLeftToday = $availableHours;
        $sessions = [];

        foreach ($remaining as $id => $task) {
            if ($hoursLeftToday <= 0) {
                break;
            }

            if ($date->gt($task['deadline']) || $task['hours'] <= 0) {
                continue;
            }

            $hoursForThisSession = min($task['hours'], $hoursLeftToday);

            $sessions[] = [
                'task_id' => $task['task_id'],
                'title'   => $task['title'],
                'hours'   => $hoursForThisSession,
            ];

            $remaining[$id]['hours'] -= $hoursForThisSession;
            $hoursLeftToday -= $hoursForThisSession;
        }

       $days[] = [
    'date'     => $date->toDateString(),
    'day_name' => $date->format('l'), 
    'sessions' => $sessions,
];

        $date->addDay();
    }

    $warnings = collect($remaining)
        ->filter(fn ($task) => $task['hours'] > 0)
        ->map(fn ($task) => [
            'task_id'       => $task['task_id'],
            'title'         => $task['title'],
            'hours_missing' => round($task['hours'], 2),
            'message'       => "\"{$task['title']}\" won't be fully covered before its deadline with the available hours.",
        ])
        ->values()
        ->all();

    return [
        'days'     => $days,
        'warnings' => $warnings,
    ];
}

    private function priorityWeight(string $priority): int
    {
        return match ($priority) {
            'high' => 3,
            'mid'  => 2,
            'low'  => 1,
            default => 0,
        };
    }
}