<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_invoice' => 'required|integer|min:1',
            'tanggal_invoice' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'no_invoice.required' => 'No Invoice wajib diisi.',
            'tanggal_invoice.required' => 'Tanggal Invoice wajib diisi.',
        ];
    }
}
