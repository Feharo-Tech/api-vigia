<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CertificateStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'type' => ['required', Rule::in(['PEM', 'PFX'])],
            'file' => 'required|file',
            'password' => 'required_if:type,PFX|string|nullable'
        ];
    }

    public function messages()
    {
        return [
            'password.required_if' => 'A senha é obrigatória para certificados do tipo PFX.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
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
