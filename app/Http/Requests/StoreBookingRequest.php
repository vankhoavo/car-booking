<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge(['phone' => preg_replace('/\s+/', '', (string) $this->input('phone'))]);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'], 'phone' => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/'], 'email' => ['required', 'email:rfc', 'max:190'],
            'pickup_location' => ['required', 'string', 'max:255'], 'destination' => ['required', 'string', 'max:255'], 'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'], 'passengers' => ['required', 'integer', 'min:1', 'max:60'],
            'vehicle_id' => ['nullable', 'integer', Rule::exists(Vehicle::class, 'id')->where(fn ($query) => $query->where('status', 'available'))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['vehicle_id.exists' => 'Xe đã chọn hiện không khả dụng. Vui lòng chọn xe khác.', 'passengers.max' => 'Số hành khách vượt quá giới hạn cho phép.'];
    }
}
