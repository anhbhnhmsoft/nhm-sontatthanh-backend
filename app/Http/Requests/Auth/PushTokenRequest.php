<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PushTokenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expo_push_token' => [
                'required',
                'string'
            ],
            'device_id' => [
                'nullable',
                'string'
            ],
            'device_type' => [
                'nullable',
                'string',
                'in:ios,android'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'expo_push_token.required' => 'Vui lòng nhập expo_push_token',
            'device_id.string' => 'device_id phải là string',
            'device_type.string' => 'device_type phải là string',
            'device_type.in' => 'device_type phải là ios hoặc android',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
