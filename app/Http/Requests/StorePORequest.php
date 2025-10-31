<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePORequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_number'      => 'nullable|string|max:50',
            'no_surat_jalan_nomor' => 'required|string',
            'no_surat_jalan_pt'    => 'required|string',
            'no_surat_jalan_tahun' => 'required|integer',
            'no_po'                => 'required|string',
            'no_invoice_nomor'     => 'nullable|string|max:255',
            'no_invoice_pt'        => 'nullable|string|max:255',
            'no_invoice_tanggal'   => 'nullable|integer|min:1|max:12',
            'no_invoice_tahun'     => 'nullable|integer',
            'customer_id'          => 'nullable|exists:customers,id',
            'tanggal_po'           => 'nullable|date',
            'kendaraan'            => 'nullable|string',
            'no_polisi'            => 'nullable|string',
            'address_1'            => 'required|string|max:255',
            'address_2'            => 'nullable|string|max:255',
            'pengirim'             => 'nullable|string|max:255',
            'items'                => 'required|array|min:1',
            'items.*.produk_id'    => 'required|exists:produks,id',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.qty_jenis'    => 'required|in:PCS,SET',
            'items.*.harga'        => 'nullable|integer|min:0',
            'items.*.total'        => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal 1 item harus diisi.',
            'items.*.produk_id.required' => 'Produk harus dipilih.',
            'items.*.qty.required' => 'Qty harus diisi.',
            'no_po.required' => 'No PO wajib diisi.',
            'address_1.required' => 'Alamat 1 wajib diisi.',
        ];
    }
}
