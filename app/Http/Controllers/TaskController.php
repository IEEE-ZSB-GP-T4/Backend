<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with('course')
            ->whereHas('course', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->latest()
            ->get();

        return ApiResponse::response(
            200,
            'Tasks retrieved successfully',
            $tasks
        );
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();

        $course = $request->user()
            ->courses()
            ->find($data['course_id']);

        if (! $course) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this course',
                null
            );
        }

        $task = $course->tasks()->create($data);

        return ApiResponse::response(
            201,
            'Task created successfully',
            $task->load('course')
        );
    }

    public function show(Request $request, Task $task)
    {
        if ($task->course->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this task',
                null
            );
        }

        return ApiResponse::response(
            200,
            'Task retrieved successfully',
            $task->load('course')
        );
    }

    public function update(
        UpdateTaskRequest $request,
        Task $task
    ) {
        if ($task->course->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this task',
                null
            );
        }

        $data = $request->validated();

        if (isset($data['course_id'])) {
            $course = $request->user()
                ->courses()
                ->find($data['course_id']);

            if (! $course) {
                return ApiResponse::response(
                    403,
                    'Unauthorized access to this course',
                    null
                );
            }
        }

        $task->update($data);

        return ApiResponse::response(
            200,
            'Task updated successfully',
            $task->fresh()->load('course')
        );
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->course->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this task',
                null
            );
        }

        $task->delete();

        return ApiResponse::response(
            200,
            'Task deleted successfully',
            null
        );
    }

    public function complete(Request $request, Task $task)
    {
        if ($task->course->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this task',
                null
            );
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return ApiResponse::response(
            200,
            'Task completed successfully',
            $task->fresh()->load('course')
        );
    }

    /**
     * Get upcoming deadlines for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upcomingDeadlines(Request $request)
    {
        $tasks = Task::with('course')
            ->whereHas('course', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->where('deadline', '>', now())
            ->where('status', '!=', 'completed')
            ->orderBy('deadline', 'asc')
            ->limit(5) // Limit to 5 upcoming tasks
            ->get();

        return ApiResponse::response(
            200,
            'Upcoming deadlines retrieved successfully',
            $tasks
        );
    }
}
