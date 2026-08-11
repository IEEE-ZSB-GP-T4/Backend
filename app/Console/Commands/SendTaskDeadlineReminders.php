<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendTaskDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:send-deadline-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send email/notification reminders for tasks nearing their deadline';

    public function handle(): int
    {
        $firstCount = $this->sendFirstReminders();
        $secondCount = $this->sendSecondReminders();

        $this->info("Sent {$firstCount} first reminder(s) and {$secondCount} second reminder(s).");

        return self::SUCCESS;
    }

    /**
     * Reminder sent when a task is due within the next 24 hours.
     */
    private function sendFirstReminders(): int
    {
        $tasks = Task::query()
            ->whereNull('reminder_sent_at')
            ->whereNotIn('status', ['completed'])
            ->whereBetween('deadline', [now(), now()->addDay()])
            ->with('course.user')
            ->get();

        foreach ($tasks as $task) {
            NotificationService::send(
                $task->course->user,
                'Reminder: Task deadline approaching',
                "Your task \"{$task->title}\" is due tomorrow ({$task->deadline->format('Y-m-d H:i')})"
            );

            $task->update(['reminder_sent_at' => now()]);
        }

        return $tasks->count();
    }

    /**
     * Reminder sent when a task is due within the next 2 hours.
     */
    private function sendSecondReminders(): int
    {
        $tasks = Task::query()
            ->whereNull('second_reminder_sent_at')
            ->whereNotIn('status', ['completed'])
            ->whereBetween('deadline', [now(), now()->addHours(2)])
            ->with('course.user')
            ->get();

        foreach ($tasks as $task) {
            NotificationService::send(
                $task->course->user,
                'Reminder: Task deadline very soon',
                "Your task \"{$task->title}\" is due in less than 2 hours ({$task->deadline->format('Y-m-d H:i')})"
            );

            $task->update(['second_reminder_sent_at' => now()]);
        }

        return $tasks->count();
    }
}