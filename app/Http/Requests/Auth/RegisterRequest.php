<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'regex:/^(0|\+84)[3|5|7|8|9][0-9]{8}$/',
                'unique:users,phone',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:50',
            ],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.unique' => 'Số điện thoại đã được đăng ký',

            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự',
            'password.max' => 'Mật khẩu không được vượt quá :max ký tự',

            'name.required' => 'Vui lòng nhập họ tên',
            'name.min' => 'Họ tên phải có ít nhất :min ký tự',
            'name.max' => 'Họ tên không được vượt quá :max ký tự',
            'name.regex' => 'Họ tên chỉ được chứa chữ cái và khoảng trắng',
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
