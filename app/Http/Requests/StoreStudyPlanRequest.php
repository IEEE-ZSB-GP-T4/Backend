<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'available_hours' => ['required', 'numeric', 'min:0.5', 'max:16'],
            'task_ids'         => ['required', 'array', 'min:1'],
            'task_ids.*'       => [
                'integer',
                Rule::exists('tasks', 'id')->where(function ($query) {
                    $query->whereIn('course_id', function ($sub) {
                        $sub->select('id')
                            ->from('courses')
                            ->where('user_id', $this->user()->id);
                    });
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'available_hours.required' => 'Please specify the available study hours.',
            'available_hours.min'      => 'The minimum study time is half an hour.',
            'available_hours.max'      => 'The maximum study time is 16 hours per day.',
            'task_ids.required'        => 'Please select at least one task.',
            'task_ids.min'             => 'You must select at least one task.',
            'task_ids.*.exists'        => 'One of the selected tasks does not exist or does not belong to you.',
        ];
    }
}