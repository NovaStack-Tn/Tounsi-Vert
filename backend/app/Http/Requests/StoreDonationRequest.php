<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1.01|max:1000000',
            'organization_id' => 'required|exists:organizations,id',
            'event_id' => 'nullable|exists:events,id',
            'status' => 'nullable|in:pending,succeeded,failed,refunded',
            'payment_ref' => 'nullable|string|max:191',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'The donation amount is required.',
            'amount.min' => 'The donation amount must be greater than 1.',
            'amount.max' => 'The donation amount cannot exceed 1,000,000.',
            'organization_id.required' => 'Please select an organization.',
            'organization_id.exists' => 'The selected organization does not exist.',
            'event_id.exists' => 'The selected event does not exist.',
            'status.in' => 'Invalid donation status.',
        ];
    }
}
