<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreStudyPlanRequest;
use App\Http\Resources\StudyPlanResource;
use App\Models\StudyPlan;
use App\Models\Task;
use App\Services\FakeStudyPlanGeneratorService;
use Illuminate\Http\Request;

class StudyPlanController extends Controller
{
    public function __construct(
        // TODO: لما ييجي Grok API key، بدّلي دي بـ:
        // private readonly StudyPlanGeneratorService $generator

        private readonly FakeStudyPlanGeneratorService $generator
    ) {}

    /**
     * GET /api/study-plan/tasks
     *
     * Get all incomplete tasks grouped by course.
     */
    public function tasksForChecklist(Request $request)
    {
        $courses = $request->user()
            ->courses()
            ->with([
                'tasks' => function ($query) {
                    $query->where('status', '!=', 'completed')
                        ->orderBy('deadline');
                },
            ])
            ->get([
                'id',
                'name',
                'code',
            ]);

        return ApiResponse::response(
            200,
            'Tasks retrieved successfully',
            $courses
        );
    }

    /**
     * POST /api/study-plan
     *
     * Generate and save a new study plan.
     */
    public function store(StoreStudyPlanRequest $request)
    {
        $user = $request->user();

        $tasks = Task::whereIn(
            'id',
            $request->task_ids
        )->get();

        $generatedPlan = $this->generator->generate(
            (float) $request->available_hours,
            $tasks
        );

        $studyPlan = StudyPlan::create([
            'user_id' => $user->id,

            'available_hours' => $request->available_hours,

            'generated_plan' => $generatedPlan,
        ]);

        return ApiResponse::response(
            201,
            'Study plan generated successfully',
            new StudyPlanResource($studyPlan)
        );
    }

    /**
     * GET /api/study-plan
     *
     * Get the latest study plan.
     */
    public function index(Request $request)
    {
        $plan = $request->user()
            ->studyPlans()
            ->latest()
            ->first();

        if (! $plan) {
            return ApiResponse::response(
                404,
                'No study plan found yet',
                null
            );
        }

        return ApiResponse::response(
            200,
            'Study plan retrieved successfully',
            new StudyPlanResource($plan)
        );
    }

    /**
     * GET /api/study-plan/history
     *
     * Get all previously generated study plans.
     */
    public function history(Request $request)
    {
        $plans = $request->user()
            ->studyPlans()
            ->latest()
            ->get();

        return ApiResponse::response(
            200,
            'Study plan history retrieved successfully',
            StudyPlanResource::collection($plans)
        );
    }

    // DELETE /api/study-plan/{studyPlan}
    public function destroy(Request $request, StudyPlan $studyPlan)
    {
        // Make sure the study plan belongs to the authenticated user
        if ($studyPlan->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this study plan',
                null
            );
        }

        $studyPlan->delete();

        return ApiResponse::response(
            200,
            'Study plan deleted successfully',
            null
        );
    }
}
