<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'profile_pic' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'name' => ['nullable', 'string', 'max:255'],
            'name_cn' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
    public function messages()
    {
        return [
            'profile_pic.image' => 'The profile picture must be an image file.',
            'profile_pic.mimes' => 'The profile picture must be a JPEG, PNG, JPG, GIF, or SVG file.',
            'profile_pic.max' => 'The profile picture may not be greater than :max kilobytes in size.',
            'name.max' => 'The name may not be greater than :max characters.',
            'name_cn.max' => 'The Chinese name may not be greater than :max characters.',
            'phone.max' => 'The phone may not be greater than :max characters.',
            'company_name.max' => 'The company name may not be greater than :max characters.',
            'department.max' => 'The department may not be greater than :max characters.',
            'designation.max' => 'The designation may not be greater than :max characters.',
        ];
    }

}