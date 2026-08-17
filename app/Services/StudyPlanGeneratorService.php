<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudyPlanGeneratorService
{
    public function generate(float $availableHours, Collection $tasks): array
    {
        if (empty(config('services.ai_planner.url'))) {
            throw new \RuntimeException('AI Planner service URL is not configured yet.');
        }

        $payload = $this->buildPayload($availableHours, $tasks);

        $response = Http::timeout(60)
            ->post(rtrim(config('services.ai_planner.url'), '/') . '/generate', $payload);

        if ($response->failed()) {
            Log::error('AI Planner service error', ['body' => $response->body()]);
            throw new \RuntimeException('AI service failed to generate the plan.');
        }

        $body = $response->json();

        // السيرفس بترجع الرد ملفوف جوه data.generated_plan
        $plan = $body['data']['generated_plan'] ?? null;

        if (! is_array($plan) || ! isset($plan['days'])) {
            Log::error('AI Planner returned an unexpected shape', ['body' => $response->body()]);
            throw new \RuntimeException('AI returned an invalid plan format.');
        }

        $plan['warnings'] = $plan['warnings'] ?? [];

        return $plan;
    }

    private function buildPayload(float $availableHours, Collection $tasks): array
    {
        $tasksPayload = $tasks->map(fn (Task $task) => [
            'task_id'         => $task->id,
            'title'           => $task->title,
            'deadline'        => Carbon::parse($task->deadline)->toDateString(),
            'priority'        => $task->priority,
            'estimated_hours' => (float) $task->estimated_hours,
        ])->values();

        return [
            'available_hours' => $availableHours,
            'tasks'           => $tasksPayload,
        ];
    }
}