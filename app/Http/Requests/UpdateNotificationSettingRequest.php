<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNotificationSettingRequest extends FormRequest
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
    public function rules()
    {
        return [
            'email_notifications' => 'sometimes|accepted',
            'notification_email' => 'nullable|email',
            'notification_frequency' => 'required|integer|min:1|max:60',
        ];
    }

    public function messages()
    {
        return [
            'email_notifications.accepted' => 'Você deve aceitar receber notificações por e-mail.',
            'notification_email.email' => 'O campo de e-mail para notificações deve conter um endereço de e-mail válido.',
            'notification_frequency.required' => 'A frequência de notificações é obrigatória.',
            'notification_frequency.integer' => 'A frequência de notificações deve ser um número inteiro.',
            'notification_frequency.min' => 'A frequência de notificações deve ser no mínimo :min.',
            'notification_frequency.max' => 'A frequência de notificações não pode ser superior a :max.',
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