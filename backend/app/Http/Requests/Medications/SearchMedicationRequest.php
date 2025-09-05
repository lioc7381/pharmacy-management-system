<?php

namespace App\Http\Requests\Medications;

use Illuminate\Foundation\Http\FormRequest;

class SearchMedicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * This is a public endpoint, so no authorization is required.
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
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
        ];
    }
}
