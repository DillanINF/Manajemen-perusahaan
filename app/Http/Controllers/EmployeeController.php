<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->get();
        $totalKaryawan = Employee::count();

        return view('employees.index', compact(
            'employees', 
            'totalKaryawan'
        ));
    }

    public function create()
    {
        return view('employees.create');
    }

   public function store(Request $request)
{
    try {
        // Validasi hanya 4 field yang tersisa di database
        $data = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'posisi' => 'required|string|max:255',
        ]);

        \Log::info('Attempting to create employee with data:', $data);
        
        // Simpan ke database
        $employee = Employee::create($data);
        
        \Log::info('Employee created successfully with ID: ' . $employee->id);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil ditambahkan',
                'employee' => $employee
            ]);
        }
        
        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil ditambahkan: ' . $employee->nama_karyawan);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation Error: ' . json_encode($e->errors()));
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
        
        return redirect()->back()
            ->withInput()
            ->withErrors($e->errors());
            
    } catch (\Exception $e) {
        \Log::error('Employee Store Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
    }
}
    

    public function edit(Employee $employee)
    {
        $employees = Employee::latest()->get();
        return view('employees.index', compact('employee', 'employees'));
    }

    public function update(Request $request, Employee $employee)
    {
        try {
            // Validasi hanya 4 field yang tersisa di database
            $data = $request->validate([
                'nama_karyawan' => 'required|string|max:255',
                'no_telepon' => 'required|string|max:20',
                'alamat' => 'required|string',
                'posisi' => 'required|string|max:255',
            ]);

            $employee->update($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data karyawan berhasil diperbarui',
                    'employee' => $employee
                ]);
            }

            return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil diperbarui.');
            
        } catch (\Exception $e) {
            \Log::error('Error update employee: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal update karyawan: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dihapus'
            ]);
        }
        
        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}
