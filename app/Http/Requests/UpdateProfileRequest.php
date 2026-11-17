<?php

namespace App\Http\Requests;

use App\Support\Governorates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Prepare inputs before validation (e.g. skills JSON decode for multipart).
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('skills') && is_string($this->skills)) {
            $decoded = json_decode($this->skills, true);
            if (is_array($decoded)) {
                $this->merge(['skills' => $decoded]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isProvider = $user?->isProvider();
        $isClient = $user?->isClient();

        $existingCity = $isProvider
            ? $user->providerProfile()->value('city')
            : ($isClient ? $user->clientProfile()->value('city') : null);

        $cityRule = function (string $attribute, mixed $value, \Closure $fail) use ($existingCity) {
            if ($value === null || $value === '') {
                return;
            }

            // If the user already had this city saved, allow it unchanged even if not in governorates list
            if ($existingCity !== null && $value === $existingCity) {
                return;
            }

            if (! Governorates::isValid((string) $value)) {
                $fail('The selected city must be one of the 24 Tunisian governorates.');
            }
        };

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'phone' => [
                $isProvider ? 'required' : 'nullable',
                'string',
                'regex:/^(?:\+216\s*)?[0-9](?:\s*[0-9]){7}$/',
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];

        if ($isClient) {
            $rules['city'] = ['nullable', 'string', 'max:100', $cityRule];
            $rules['address'] = ['nullable', 'string', 'max:255'];
        } elseif ($isProvider) {
            $rules['city'] = ['nullable', 'string', 'max:100', $cityRule];
            $rules['business_name'] = ['nullable', 'string', 'max:150'];
            $rules['category'] = ['required', 'string', 'max:100'];
            $rules['bio'] = ['nullable', 'string', 'max:1000'];
            $rules['skills'] = ['nullable', 'array'];
            $rules['skills.*'] = ['string', 'max:50'];
            $rules['hourly_rate'] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone must be a valid Tunisian phone number (8 digits, optional +216 prefix).',
            'phone.required' => 'The phone number is required for service providers.',
            'category.required' => 'The category field is required for service providers.',
        ];
    }
}
