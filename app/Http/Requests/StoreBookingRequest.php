<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/\s+/', '', (string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/'],
            'email' => ['required', 'email:rfc,dns', 'max:190'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'passengers' => ['required', 'integer', 'min:1', 'max:60'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
