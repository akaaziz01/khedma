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
            'budget'         => ['required', 'numeric', 'min:1', 'max:999999'],
            'location'       => ['required_without:city', 'nullable', 'string', 'max:255'],
            'city'           => ['required_without:location', 'nullable', 'string', 'max:100'],
            'address'        => ['nullable', 'string', 'max:255'],
            'urgency'        => ['nullable', 'in:low,medium,urgent,emergency'],
            'deadline'       => ['required_without:preferred_date', 'nullable', 'date', 'after_or_equal:today'],
            'preferred_date' => ['required_without:deadline', 'nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Please provide a descriptive title for your request.',
            'category.required'    => 'Please select a trade/category for the required service.',
            'description.required' => 'Please explain your need or project in detail.',
            'budget.required'      => 'Please provide a budget for the service request.',
            'city.required'        => 'Please specify the city where the service is needed.',
            'location.required'    => 'Please specify the location where the service is needed.',
            'deadline.required'    => 'Please provide a deadline for the service request.',
            'preferred_date.required' => 'Please provide a deadline for the service request.',
            'deadline.after_or_equal' => 'The deadline must be today or a future date.',
            'preferred_date.after_or_equal' => 'The deadline must be today or a future date.',
        ];
    }
}
