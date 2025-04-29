<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class UserUpdateRequest extends ApiStoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = $this->route('user');

        $userId = $user instanceof \App\Models\User ? $user->id : $user;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome não pode ter mais de :max caracteres.',

            'email.required' => 'O campo e-mail é obrigatório.',
            'email.string' => 'O campo e-mail deve ser uma string.',
            'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
            'email.max' => 'O campo e-mail não pode ter mais de :max caracteres.',
            'email.unique' => 'Este e-mail já está em uso.',
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
