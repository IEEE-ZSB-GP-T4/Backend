<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Total Courses
        |--------------------------------------------------------------------------
        */

        $totalCourses = $user->courses()->count();


        /*
        |--------------------------------------------------------------------------
        | Pending Tasks
        |--------------------------------------------------------------------------
        */

        $pendingTasks = Task::whereHas('course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'pending')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Study Hours This Week
        |--------------------------------------------------------------------------
        */

        $studyHoursThisWeek = Task::whereHas('course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'completed')
            ->whereBetween('completed_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('estimated_hours');


        /*
        |--------------------------------------------------------------------------
        | Upcoming Deadlines
        |--------------------------------------------------------------------------
        */

        $upcomingDeadlines = Task::with([
            'course:id,name,code'
        ])
            ->whereHas('course', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', 'pending')
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | AI Insight
        |--------------------------------------------------------------------------
        |
        */

        $aiInsight = $this->generateInsight(
            $pendingTasks,
            $upcomingDeadlines
        );


        return ApiResponse::response(
            200,
            'Dashboard retrieved successfully',
            [
                'summary' => [
                    'total_courses' => $totalCourses,
                    'pending_tasks' => $pendingTasks,
                    'study_hours_this_week' => (float) $studyHoursThisWeek,
                ],

                'upcoming_deadlines' => $upcomingDeadlines,

                'ai_insight' => [
                    'message' => $aiInsight
                ]
            ]
        );
    }


    /**
     * Generate simple insight.
     */
    private function generateInsight(
        int $pendingTasks,
        $upcomingDeadlines
    ): string {

        if ($pendingTasks === 0) {
            return 'Great job! You have completed all your pending tasks.';
        }

        if ($upcomingDeadlines->count() >= 3) {
            return 'You have several upcoming deadlines. Consider organizing your study schedule and prioritizing urgent tasks.';
        }

        if ($upcomingDeadlines->count() > 0) {
            return 'You have upcoming deadlines. Make sure to complete your highest priority tasks first.';
        }

        return 'Your workload looks manageable. Keep following your study plan consistently.';
    }
}
