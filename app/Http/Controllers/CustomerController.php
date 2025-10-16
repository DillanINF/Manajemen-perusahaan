<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'address_1' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:500',
            'address_3' => 'nullable|string|max:500',
            'payment_terms_days' => 'nullable|integer|min:1|max:365',
            'delivery_note_nomor' => 'nullable|string|max:50',
            'delivery_note_pt' => 'nullable|string|max:50',
            'delivery_note_tahun' => 'nullable|string|max:10',
            'invoice_nomor' => 'nullable|string|max:50',
            'invoice_pt' => 'nullable|string|max:50',
            'invoice_tahun' => 'nullable|string|max:10',
        ]);

        $deliveryNoteParts = [
            $validated['delivery_note_nomor'] ?? '',
            $validated['delivery_note_pt'] ?? '',
            $validated['delivery_note_tahun'] ?? ''
        ];
        
        // Only set delivery_note_number if at least one part has value
        if (array_filter($deliveryNoteParts)) {
            $validated['delivery_note_number'] = implode('/', $deliveryNoteParts);
        }

        // Remove individual delivery note fields as they're not in database
        unset($validated['delivery_note_nomor'], $validated['delivery_note_pt'], $validated['delivery_note_tahun']);

        // Compose invoice number
        $invoiceParts = [
            $validated['invoice_nomor'] ?? '',
            $validated['invoice_pt'] ?? '',
            $validated['invoice_tahun'] ?? ''
        ];
        if (array_filter($invoiceParts)) {
            $validated['invoice_number'] = implode('/', $invoiceParts);
        }
        unset($validated['invoice_nomor'], $validated['invoice_pt'], $validated['invoice_tahun']);

        // code_number akan tersimpan jika dikirim dari form
        
        Customer::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Customer berhasil ditambahkan']);
        }

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan');
    }

    public function show(Customer $customer)
    {
        return view('customer.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Validasi hanya field yang dikirim dari form edit
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'address_1' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:500',
        ]);

        $customer->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Customer berhasil diperbarui']);
        }

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Customer berhasil dihapus']);
        }

        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus');
    }
}
