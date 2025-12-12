<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class VerifyOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'regex:/^(0|\+84)[3|5|7|8|9][0-9]{8}$/',
            ],
            'otp' => [
                'required',
                'string',
                'digits:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại không đúng định dạng',

            'otp.required' => 'Vui lòng nhập mã OTP',
            'otp.digits' => 'Mã OTP phải có đúng :digits chữ số',
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
