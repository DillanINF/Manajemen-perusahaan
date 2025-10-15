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
        $employees = Employee::aktif()->get();
        
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
        $employees = Employee::aktif()->get();
        return view('salary.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
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

        // Cek duplikasi gaji untuk bulan dan tahun yang sama
        $existingSalary = Salary::where('employee_id', $data['employee_id'])
            ->where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->first();

        if ($existingSalary) {
            return redirect()->back()->withErrors(['error' => 'Gaji untuk karyawan ini pada bulan dan tahun tersebut sudah ada.']);
        }

        Salary::create($data);

        return redirect()->route('salary.index')->with('success', 'Data gaji berhasil ditambahkan.');
    }

    public function edit(Salary $salary)
    {
        $employees = Employee::aktif()->get();
        $salaries = Salary::latest()->get();
        return view('salary.index', compact('salary', 'salaries', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
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

        // Cek duplikasi gaji untuk bulan dan tahun yang sama (kecuali record yang sedang diupdate)
        $existingSalary = Salary::where('employee_id', $data['employee_id'])
            ->where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->where('id', '!=', $salary->id)
            ->first();

        if ($existingSalary) {
            return redirect()->back()->withErrors(['error' => 'Gaji untuk karyawan ini pada bulan dan tahun tersebut sudah ada.']);
        }

        $salary->update($data);

        return redirect()->route('salary.index')->with('success', 'Data gaji berhasil diperbarui.');
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
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        $employees = Employee::aktif()->get();
        $createdCount = 0;

        foreach ($employees as $employee) {
            // Cek apakah gaji untuk bulan ini sudah ada
            $existingSalary = Salary::where('employee_id', $employee->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            if (!$existingSalary) {
                $gajiPokok = (int)$employee->gaji_pokok;
                $tunjangan = (int)$employee->tunjangan;
                $potonganPajak = (int)($gajiPokok * 0.05); // 5% pajak
                $potonganBpjs = (int)($gajiPokok * 0.02); // 2% BPJS
                $totalGaji = ($gajiPokok + $tunjangan) - ($potonganPajak + $potonganBpjs);

                Salary::create([
                    'employee_id' => $employee->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'gaji_pokok' => $gajiPokok,
                    'tunjangan' => $tunjangan,
                    'bonus' => 0,
                    'lembur' => 0,
                    'potongan_pajak' => $potonganPajak,
                    'potongan_bpjs' => $potonganBpjs,
                    'potongan_lain' => 0,
                    'total_gaji' => $totalGaji,
                    'status_pembayaran' => 'belum_dibayar',
                    'keterangan' => 'Auto-generated payroll'
                ]);
                $createdCount++;
            }
        }

        return redirect()->route('salary.index')->with('success', "Payroll berhasil dibuat untuk {$createdCount} karyawan.");
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
