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
    if (empty(config('grok.api_key'))) {
        throw new \RuntimeException('Grok API key is not configured yet.');
    }

    $prompt = $this->buildPrompt($availableHours, $tasks);

    $response = Http::withToken(config('grok.api_key'))
        ->timeout(60)
        ->post(config('grok.api_url'), [
            'model' => config('grok.model'),
            'messages' => [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.3,
        ]);

    if ($response->failed()) {
        Log::error('Grok API error', ['body' => $response->body()]);
        throw new \RuntimeException('AI service failed to generate the plan.');
    }

    $content = $response->json('choices.0.message.content');
    $plan = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($plan['days'])) {
        Log::error('Grok returned invalid JSON', ['content' => $content]);
        throw new \RuntimeException('AI returned an invalid plan format.');
    }

    $plan['warnings'] = $plan['warnings'] ?? [];

    return $plan;
}
    

   private function systemPrompt(): string
{
    return <<<TEXT
You are a study planning assistant. You receive a student's daily available
study hours (same value every day) and a list of tasks (each with estimated
hours, a deadline, and a priority). Distribute the tasks across the days
from today until the last deadline among the given tasks, without exceeding
the daily available hours on any single day. Prioritize tasks with earlier
deadlines and higher priority. You may split a single task's hours across
multiple days if needed.

If a task cannot be fully completed before its deadline given the available
hours, do NOT drop it silently — instead list it in a "warnings" array
explaining how many hours are still missing.

Respond ONLY with valid JSON in this exact shape, with no extra text before
or after it:
{
  "days": [
    {
      "date": "YYYY-MM-DD",
      "sessions": [
        {"task_id": number, "title": string, "hours": number}
      ]
    }
  ],
  "warnings": [
    {"task_id": number, "title": string, "hours_missing": number, "message": string}
  ]
}
TEXT;
}

    private function buildPrompt(float $availableHours, Collection $tasks): string
    {
        $today = Carbon::today()->toDateString();

        $tasksPayload = $tasks->map(fn (Task $task) => [
            'task_id'         => $task->id,
            'title'           => $task->title,
            'estimated_hours' => (float) $task->estimated_hours,
            'deadline'        => Carbon::parse($task->deadline)->toDateString(),
            'priority'        => $task->priority,
        ])->values();

        return json_encode([
            'today'                    => $today,
            'available_hours_per_day'  => $availableHours,
            'tasks'                    => $tasksPayload,
        ], JSON_UNESCAPED_UNICODE);
    }
}