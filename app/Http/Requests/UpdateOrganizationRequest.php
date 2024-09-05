<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phoneNo' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];        
    }

    public function messages()
    {
        return [
            'name.required' => 'The organization name is required.',
            'name.string' => 'The organization name must be a string.',
            'name.max' => 'The organization name may not be greater than 255 characters.',
            'address.required' => 'The address is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address may not be greater than 255 characters.',
            'phoneNo.required' => 'The phone number is required.',
            'phoneNo.string' => 'The phone number must be a string.',
            'phoneNo.max' => 'The phone number may not be greater than 20 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'logo.image' => 'The logo must be an image.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, png, jpg, gif.',
            'logo.max' => 'The logo may not be greater than 2048 kilobytes.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed for organization update', [
            'errors' => $validator->errors()->toArray(),
        ]);

        // Get all the specific error messages
        $errors = $validator->errors()->all();

        // Combine the specific errors with the custom message
        $combinedMessage = implode(' ', $errors) . ' The form data was retained. Please return to edit mode to continue editing the data.';

        throw new ValidationException($validator, response()->redirectToRoute('admin.organization')
            ->withInput()
            ->withErrors(['error' => $combinedMessage])
        );
    }
}
