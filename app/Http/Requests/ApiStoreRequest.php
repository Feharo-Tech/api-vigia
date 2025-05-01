<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE,OPTIONS',
            'expected_status_code' => 'required|integer|between:100,599',
            'expected_response' => 'nullable|string',
            'check_interval' => 'required|integer|min:1',
            'content_type' => 'nullable|string|max:100',
            'is_active' => 'sometimes|accepted',
            'headers' => 'nullable|json',
            'body' => 'nullable|json',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'error_threshold' => 'nullable|integer|min:1',
            'timeout_threshold' => 'nullable|integer|min:1',
            'should_notify' => 'sometimes|accepted',
            'certificate_id' => 'nullable|integer|min:1',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ])
        );
    }
}
