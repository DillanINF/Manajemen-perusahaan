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
        'no_telepon' => 'required|string|max:20',
        'alamat' => 'required|string',
        'posisi' => 'required|string|max:255',
        'email' => 'nullable|email|unique:employees,email',
        'departemen' => 'nullable|string|max:255',
        'gaji_pokok' => 'nullable|numeric|min:0',
        'status' => 'nullable|in:aktif,tidak_aktif',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    // Mirror legacy kolom 'name' untuk kompatibilitas schema lama (NOT NULL)
    $data['name'] = $data['nama_karyawan'];
    
    // Set safe defaults untuk kolom opsional
    if (!isset($data['email']) || empty($data['email'])) {
        // Generate unique email dari nama + timestamp
        $data['email'] = strtolower(str_replace(' ', '', $data['nama_karyawan'])) . time() . '@placeholder.com';
    }
    if (!isset($data['departemen']) || $data['departemen'] === null || $data['departemen'] === '') {
        $data['departemen'] = 'Umum';
    }
    if (!isset($data['status']) || empty($data['status'])) {
        $data['status'] = 'aktif';
    }
    if (!isset($data['gaji_pokok']) || $data['gaji_pokok'] === null) {
        $data['gaji_pokok'] = 0;
    }

    if ($request->hasFile('foto')) {
        $data['foto'] = $request->file('foto')->store('employees', 'public');
    }

    try {
        \Log::info('Attempting to create employee with data:', $data);
        
        // Use DB transaction to ensure data is committed
        \DB::beginTransaction();
        
        $employee = Employee::create($data);
        
        \Log::info('Employee object created with ID: ' . $employee->id);
        
        // Verify in database immediately
        $verifyEmployee = \DB::table('employees')->where('id', $employee->id)->first();
        
        if ($verifyEmployee) {
            \Log::info('Verified employee exists in database:', (array)$verifyEmployee);
            \DB::commit();
            
            return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil ditambahkan dengan ID: ' . $employee->id . ' | Nama: ' . $employee->nama_karyawan);
        } else {
            \Log::error('Employee not found in database after create!');
            \DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Data gagal diverifikasi di database']);
        }
        
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Employee Store Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
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
        $data = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'posisi' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'departemen' => 'nullable|string|max:255',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:aktif,tidak_aktif',
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
        
        // Preserve atau set safe defaults
        if (!isset($data['email']) || empty($data['email'])) {
            $data['email'] = $employee->email ?? (strtolower(str_replace(' ', '', $data['nama_karyawan'])) . time() . '@placeholder.com');
        }
        if (!isset($data['departemen']) || $data['departemen'] === null || $data['departemen'] === '') {
            $data['departemen'] = $employee->departemen ?? 'Umum';
        }
        if (!isset($data['status']) || empty($data['status'])) {
            $data['status'] = $employee->status ?? 'aktif';
        }
        if (!array_key_exists('gaji_pokok', $data) || $data['gaji_pokok'] === null) {
            $data['gaji_pokok'] = $employee->gaji_pokok ?? 0;
        }

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
