<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::latest()->get();
        $employees = Employee::all(); // Hapus scope aktif() yang sudah tidak ada
        
        // Daftar tahun untuk modal (dari 2020 sampai 2035)
        $allYears = range(2020, 2035);
        
        // Statistik
        $totalGajiDibayar = Salary::dibayar()->sum('total_gaji');
        $totalGajiBelumDibayar = Salary::belumDibayar()->sum('total_gaji');
        $jumlahKaryawanDibayar = Salary::dibayar()->count();
        $rataRataGaji = Salary::dibayar()->avg('total_gaji') ?? 0;

        // DINONAKTIFKAN PERMANENT: Excel rendering terlalu lambat (5-10 detik loading)
        // Gunakan tabel manual saja yang sudah ada di view
        $gajiTemplateError = null;
        $gajiBoronganHtml = null;
        $gajiXlsxError = null;
        $gajiXlsxHtml = null;

        return view('salary.index', compact(
            'salaries', 
            'employees',
            'totalGajiDibayar',
            'totalGajiBelumDibayar', 
            'jumlahKaryawanDibayar',
            'rataRataGaji',
            'allYears',
            'gajiTemplateError',
            'gajiBoronganHtml',
            'gajiXlsxHtml',
            'gajiXlsxError'
        ));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('salary.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_gaji' => 'required|in:borongan,harian',
            'jumlah_hari' => 'nullable|integer|min:1',
            'tarif_harian' => 'nullable|integer|min:0',
            'jumlah_unit' => 'nullable|integer|min:1',
            'tarif_per_unit' => 'nullable|integer|min:0',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030',
            'tunjangan' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'lembur' => 'nullable|numeric|min:0',
            'potongan_pajak' => 'nullable|numeric|min:0',
            'potongan_bpjs' => 'nullable|numeric|min:0',
            'potongan_lain' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'required|in:belum_dibayar,dibayar',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string'
        ]);

        // Validasi berdasarkan jenis gaji
        if ($data['jenis_gaji'] === 'harian') {
            if (empty($data['jumlah_hari']) || empty($data['tarif_harian'])) {
                return redirect()->back()->withErrors(['error' => 'Jumlah hari dan tarif harian harus diisi untuk gaji harian.']);
            }
            $data['gaji_pokok'] = $data['jumlah_hari'] * $data['tarif_harian'];
        } else { // borongan
            if (empty($data['jumlah_unit']) || empty($data['tarif_per_unit'])) {
                return redirect()->back()->withErrors(['error' => 'Jumlah unit dan tarif per unit harus diisi untuk gaji borongan.']);
            }
            $data['gaji_pokok'] = $data['jumlah_unit'] * $data['tarif_per_unit'];
        }

        $data['tunjangan'] = (int)($data['tunjangan'] ?? 0);
        $data['bonus'] = (int)($data['bonus'] ?? 0);
        $data['lembur'] = (int)($data['lembur'] ?? 0);
        $data['potongan_pajak'] = (int)($data['potongan_pajak'] ?? 0);
        $data['potongan_bpjs'] = (int)($data['potongan_bpjs'] ?? 0);
        $data['potongan_lain'] = (int)($data['potongan_lain'] ?? 0);

        // Hitung total gaji
        $totalPendapatan = $data['gaji_pokok'] + $data['tunjangan'] + $data['bonus'] + $data['lembur'];
        $totalPotongan = $data['potongan_pajak'] + $data['potongan_bpjs'] + $data['potongan_lain'];
        $data['total_gaji'] = $totalPendapatan - $totalPotongan;

        // Cek duplikasi berdasarkan bulan dan tahun saja (karena employee_id sudah dihapus)
        $existingSalary = Salary::where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->first();

        if ($existingSalary) {
            return redirect()->back()->withErrors(['error' => 'Data gaji untuk bulan dan tahun tersebut sudah ada.']);
        }

        Salary::create($data);

        return redirect()->route('salary.index')->with('success', 'Data gaji berhasil ditambahkan.');
    }

    public function edit(Salary $salary)
    {
        $employees = Employee::all();
        $salaries = Salary::latest()->get();
        return view('salary.index', compact('salary', 'salaries', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        try {
            // Validasi hanya field yang pasti ada di database
            $validated = $request->validate([
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2020|max:2030',
                'gaji_pokok' => 'required|numeric|min:0',
            ]);

            // Hitung total gaji (sama dengan gaji_pokok jika tidak ada komponen lain)
            $gajiPokok = (float)$validated['gaji_pokok'];
            $totalGaji = $gajiPokok;

            // Cek duplikasi berdasarkan bulan dan tahun saja (kecuali record yang sedang diupdate)
            $existingSalary = Salary::where('bulan', $validated['bulan'])
                ->where('tahun', $validated['tahun'])
                ->where('id', '!=', $salary->id)
                ->first();

            if ($existingSalary) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Data gaji untuk bulan dan tahun tersebut sudah ada.'], 422);
                }
                return redirect()->back()->withErrors(['error' => 'Data gaji untuk bulan dan tahun tersebut sudah ada.']);
            }

            // Data yang akan di-update (hanya kolom yang pasti ada)
            $updateData = [
                'bulan' => $validated['bulan'],
                'tahun' => $validated['tahun'],
                'gaji_pokok' => $gajiPokok,
                'total_gaji' => $totalGaji,
            ];

            $salary->update($updateData);

            // Return JSON jika AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data gaji berhasil diperbarui',
                    'salary' => $salary
                ]);
            }

            return redirect()->route('salary.index')->with('success', 'Data gaji berhasil diperbarui.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Salary Update Error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Server Error: ' . $e->getMessage());
        }
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        
        // Check if request is AJAX
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data gaji berhasil dihapus.']);
        }
        
        return redirect()->route('salary.index')->with('success', 'Data gaji berhasil dihapus.');
    }
    
    public function storeSimple(Request $request)
    {
        $data = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2035',
            'nominal_gaji_raw' => 'required|numeric|min:1'
        ]);
        
        // Create salary record - simple version
        Salary::create([
            'bulan' => $data['bulan'],
            'tahun' => $data['tahun'],
            'gaji_pokok' => $data['nominal_gaji_raw'],
            'total_gaji' => $data['nominal_gaji_raw'],
            'status_pembayaran' => 'dibayar'
        ]);
        
        return redirect()->route('salary.index')->with('success', 'Data gaji berhasil ditambahkan.');
    }

    public function generatePayroll(Request $request)
    {
        // FITUR DINONAKTIFKAN: Kolom gaji_pokok dan tunjangan sudah tidak ada di tabel employees
        // Gaji harus diinput manual untuk setiap karyawan
        return redirect()->route('salary.index')->with('error', 'Fitur auto-generate payroll tidak tersedia. Silakan tambahkan gaji karyawan secara manual.');
    }
    
    public function toggleStatus(Salary $salary)
    {
        // Toggle status pembayaran
        $newStatus = $salary->status_pembayaran === 'dibayar' ? 'belum_dibayar' : 'dibayar';
        $salary->update([
            'status_pembayaran' => $newStatus,
            'tanggal_bayar' => $newStatus === 'dibayar' ? now() : null
        ]);
        
        return response()->json(['success' => true, 'status' => $newStatus]);
    }
}
