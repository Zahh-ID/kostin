<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'tenant';
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'preferred_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:36'],
            'occupants_count' => ['required', 'integer', 'min:1', 'max:6'],
            'budget_per_month' => ['required', 'integer', 'min:0'],
            'employment_status' => ['required', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'monthly_income' => ['required', 'integer', 'min:0'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email'],
            'has_vehicle' => ['nullable', 'boolean'],
            'vehicle_notes' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['required', 'string', 'max:150'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
            'tenant_notes' => ['nullable', 'string'],
            'terms_agreed' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_vehicle' => $this->boolean('has_vehicle'),
        ]);
    }
}
