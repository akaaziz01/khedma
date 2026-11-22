<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'price'                    => ['required', 'numeric', 'min:1', 'max:999999'],
            'delivery_time'            => ['required', 'string', 'max:100'],
            'delivery_days'            => ['nullable', 'integer', 'min:1', 'max:365'],
            'estimated_duration_hours' => ['nullable', 'numeric', 'min:0.5', 'max:500'],
            'message'                  => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required'         => 'Please specify your proposed price in TND.',
            'delivery_time.required' => 'Please provide an estimated delivery timeline (e.g. 2 days, tomorrow).',
            'message.required'       => 'Please provide a message explaining your proposal and scope of work.',
        ];
    }
}
