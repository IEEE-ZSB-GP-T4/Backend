<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => [
                'sometimes',
                'integer',
                'exists:courses,id',
            ],

            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'deadline' => [
                'sometimes',
                'date',
            ],

            'estimated_hours' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'priority' => [
                'sometimes',
                'in:low,mid,high',
            ],

            'status' => [
                'sometimes',
                'in:pending,progress,completed',
            ],
        ];
    }
}
