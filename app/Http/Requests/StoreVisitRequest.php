<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\File;

class StoreVisitRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'visit_purpose' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'description' => ['required'],
            'spk' => ['nullable', File::types(['pdf'])->max('250kb')],
            'visitors' => ['array', 'min:1'],
            'visitors.*.ktp' => ['required'],
            'visitors.*.name' => ['required'],
            'visitors.*.file_ktp' => ['required', File::types(['png','jpg','jpeg'])->max('250kb')],
            'visitors.*.company' => ['required'],
            'visitors.*.occupation' => ['required'],
            'visitors.*.phone' => ['required'],
            'visitors.*.email' => ['required', 'ends_with:gmail.com'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ];
    }

    public function messages(): array
    {
        return [
            'visit_purpose' => 'The visit purpose field is required.',
            'start_date' => 'The start date field is required.',
            'end_date' => 'The end date field is required.',
            'description' => 'The description field is required.',
            'spk.max' => 'The maximum allowed file size is 250KB.',
            'visitors.*.ktp.required' => 'The KTP field is required.',
            'visitors.*.name.required' => 'The name field is required.',
            'visitors.*.file_ktp.required' => 'The File KTP field is required.',
            'visitors.*.file_ktp.max' => 'The maximum allowed file size is 250KB.',
            'visitors.*.company.required' => 'The company field is required.',
            'visitors.*.occupation.required' => 'The occupation field is required.',
            'visitors.*.phone.required' => 'The phone field is required.',
            'visitors.*.email.required' => 'The email field is required.',
            'visitors.*.email.ends_with' => 'The email must be @gmail.com.',
        ];
    }
}
