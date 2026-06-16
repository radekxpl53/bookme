<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username'   => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'first_name' => ['required', 'string', 'max:255'],
            'surname'    => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'regex:/^(\+48)?[0-9]{9}$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Podaj poprawny numer telefonu: 9 cyfr, opcjonalnie z prefiksem +48.',
        ];
    }
}
