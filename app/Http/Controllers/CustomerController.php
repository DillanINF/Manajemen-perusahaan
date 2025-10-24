<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::oldest()->get();
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        // Validasi field yang ada di database
        // NOTE: Nama customer BOLEH DUPLIKAT (tidak ada validasi unique)
        // Ini memungkinkan input customer dengan nama yang sama berkali-kali
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address_1' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:500',
            'payment_terms_days' => 'nullable|integer|min:1|max:365',
        ]);
        
        if (!isset($validated['payment_terms_days'])) {
            $validated['payment_terms_days'] = 30;
        }
        
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
        try {
            // Validasi field yang ada di database
            // NOTE: Nama customer BOLEH DUPLIKAT (tidak ada validasi unique)
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code_number' => 'nullable|string|max:100',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address_1' => 'nullable|string|max:500',
                'address_2' => 'nullable|string|max:500',
                'payment_terms_days' => 'nullable|integer|min:1|max:365',
            ]);

            $customer->update($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Customer berhasil diperbarui',
                    'customer' => $customer
                ]);
            }

            return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error update customer: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal update customer: ' . $e->getMessage());
        }
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
