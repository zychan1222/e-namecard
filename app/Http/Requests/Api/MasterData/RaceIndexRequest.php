<?php

namespace App\Http\Requests\Api\MasterData;

use App\Http\Requests\Api\CommonApiValidationRequest;

class RaceIndexRequest extends CommonApiValidationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name' => ['nullable', 'string'],
        ]);
    }
}
