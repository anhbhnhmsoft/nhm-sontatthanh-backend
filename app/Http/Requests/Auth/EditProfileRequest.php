<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'phone' => 'required|string|regex:/^(0|\+84)[3|5|7|8|9][0-9]{8}$/',
            'old_password' => 'nullable|string|min:6',
            'new_password' => 'nullable|string|min:6',
            'email' => 'nullable|email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên',
            'name.max' => 'Tên không được vượt quá 255 ký tự',
            'avatar.image' => 'Ảnh đại diện phải là file ảnh',
            'avatar.mimes' => 'Ảnh đại diện phải là file ảnh có định dạng jpeg, png, jpg, gif',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 10MB',
            'old_password.min' => 'Mật khẩu cũ phải có ít nhất 6 ký tự',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'email.email' => 'Email không hợp lệ',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
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
