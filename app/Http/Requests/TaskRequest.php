<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'details' => 'required|string|max:65535',
            'due_date' => 'required|date|after_or_equal:today',
            'status' => 'required|in:in_progress,on_hold,completed',
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'A project ID is required.',
            'project_id.exists' => 'The selected project does not exist.',
            'created_by.required' => 'The creator of the task is required.',
            'created_by.exists' => 'The selected user does not exist.',
            'assigned_to.required' => 'The assignee is required.',
            'assigned_to.exists' => 'The selected user does not exist.',
            'title.required' => 'The task title is required.',
            'title.max' => 'The task title may not exceed 255 characters.',
            'details.required' => 'Task details are required.',
            'details.max' => 'Task details may not exceed 65535 characters.',
            'due_date.required' => 'A due date is required.',
            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after_or_equal' => 'The due date cannot be in the past.',
            'status.required' => 'The task status is required.',
            'status.in' => 'The task status must be one of: in_progress, on_hold, completed.',
        ];
    }
}
