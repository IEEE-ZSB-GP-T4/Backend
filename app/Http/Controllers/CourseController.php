<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = auth()->user()->courses()->get();
        if (count($courses) > 0) {
            return ApiResponse::response(
                200,
                'Courses retrieved successfully',
                $courses
            );
        }

        return ApiResponse::response(
            404,
            'No courses found',
            []
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        // make validation & create course
        $course = auth()->user()->courses()->create($request->validated());

        // return response
        return ApiResponse::response(
            201,
            'Course created successfully',
            $course
        );

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Course $course)
    {
        if ($course->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this course',
                null
            );
        }

        return ApiResponse::response(
            200,
            'Course retrieved successfully',
            $course
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateCourseRequest $request,
        Course $course
    ) {
        if ($course->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this course',
                null
            );
        }

        $course->update($request->validated());

        return ApiResponse::response(
            200,
            'Course updated successfully',
            $course->fresh()
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Course $course)
    {
        if ($course->user_id !== $request->user()->id) {
            return ApiResponse::response(
                403,
                'Unauthorized access to this course',
                null
            );
        }

        $course->delete();

        return ApiResponse::response(
            200,
            'Course deleted successfully',
            null
        );
    }
}
