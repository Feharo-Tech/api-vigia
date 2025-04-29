<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;



class UserStoreRequest extends ApiStoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
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

            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'A confirmação da senha não coincide.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.letters' => 'A senha deve conter pelo menos uma letra.',
            'password.numbers' => 'A senha deve conter pelo menos um número.',
            'password.symbols' => 'A senha deve conter pelo menos um símbolo.',
            'password.uncompromised' => 'A senha fornecida foi encontrada em um vazamento de dados. Por favor, escolha uma senha diferente.',
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
