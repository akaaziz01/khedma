<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isClient();
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', 'max:100'],
            'description'    => ['required', 'string', 'min:10', 'max:5000'],
            'budget'         => ['nullable', 'numeric', 'min:1', 'max:999999'],
            'city'           => ['required', 'string', 'max:100'],
            'address'        => ['nullable', 'string', 'max:255'],
            'urgency'        => ['nullable', 'in:low,medium,urgent,emergency'],
            'preferred_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Please provide a descriptive title for your request.',
            'category.required'    => 'Please select a trade/category for the required service.',
            'description.required' => 'Please explain your need or project in detail.',
            'city.required'        => 'Please specify the city where the service is needed.',
        ];
    }
}
