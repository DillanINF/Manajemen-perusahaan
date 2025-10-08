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
        $karyawanAktif = Employee::aktif()->count();
        $totalGaji = Employee::aktif()->sum('gaji_pokok');
        $rataRataGaji = Employee::aktif()->count() > 0 ? $totalGaji / Employee::aktif()->count() : 0;

        return view('employees.index', compact(
            'employees', 
            'totalKaryawan', 
            'karyawanAktif', 
            'totalGaji', 
            'rataRataGaji'
        ));
    }

    public function create()
    {
        return view('employees.create');
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'nama_karyawan' => 'required|string|max:255',
        'email' => 'required|email|unique:employees,email',
        'no_telepon' => 'required|string|max:20',
        'alamat' => 'required|string',
        'posisi' => 'required|string|max:255',
        'departemen' => 'required|string|max:255', // ✅ tambahkan ini
        'gaji_pokok' => 'required|numeric|min:0',
        'status' => 'required|in:aktif,tidak_aktif',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    // Mirror legacy kolom 'name' untuk kompatibilitas schema lama (NOT NULL)
    $data['name'] = $data['nama_karyawan'];

    if ($request->hasFile('foto')) {
        $data['foto'] = $request->file('foto')->store('employees', 'public');
    }

    Employee::create($data);

    return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil ditambahkan.');
}
    

    public function edit(Employee $employee)
    {
        $employees = Employee::latest()->get();
        return view('employees.index', compact('employee', 'employees'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'posisi' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,tidak_aktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($employee->foto) {
                Storage::disk('public')->delete($employee->foto);
            }
            $data['foto'] = $request->file('foto')->store('employees', 'public');
        }

        // Mirror legacy kolom 'name' untuk kompatibilitas schema lama (NOT NULL)
        $data['name'] = $data['nama_karyawan'];

        $employee->update($data);

        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        // Delete foto if exists
        if ($employee->foto) {
            Storage::disk('public')->delete($employee->foto);
        }

        $employee->delete();
        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}
