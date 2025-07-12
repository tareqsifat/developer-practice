<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'stack_id' => 'required|exists:stacks,id',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'question_text' => 'required|string',
            'type' => 'required|in:mcq,short_answer,long_answer',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|integer|min:1',
            'explanation' => 'nullable|string'
        ];
    }
}
