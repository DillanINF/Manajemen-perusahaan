<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\POItem;
use App\Models\Salary;
use App\Models\Expense;
use App\Services\DatabaseService;

class FinanceController extends Controller
{
    // Halaman Finance - Pendapatan
    public function income(Request $request)
    {
        $incMonth = (int) ($request->get('inc_month') ?? $request->get('month') ?? (int) Carbon::now()->format('n'));
        $incYear  = (int) ($request->get('inc_year')  ?? $request->get('year')  ?? (int) Carbon::now()->format('Y'));
        
        // Array tahun untuk modal
        $allYears = range(2020, 2035);

        $start = Carbon::create($incYear, $incMonth, 1)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        // Net (subtotal) berasal dari total POItem pada bulan/tahun dipilih
        $monthlySubtotal = (int) POItem::whereHas('po', function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_po', [$start, $end]);
            })
            ->sum('total');
        $monthlyPpn     = (int) round($monthlySubtotal * 0.11);
        $monthlyRevenue = (int) ($monthlySubtotal + $monthlyPpn); // Bruto = Net + PPN

        // Ringkasan per customer untuk bulan dipilih (NET)
        $revenueByCustomer = POItem::select('pos.customer',
                DB::raw('COUNT(DISTINCT po_items.po_id) as orders'),
                DB::raw('SUM(po_items.total) as subtotal')
            )
            ->join('pos', 'po_items.po_id', '=', 'pos.id')
            ->whereBetween('pos.tanggal_po', [$start, $end])
            ->groupBy('pos.customer')
            ->orderByDesc('subtotal')
            ->get();

        // Net per bulan sepanjang tahun (untuk tabel per bulan)
        $revenueNetByMonthRaw = [];
        
        // Get data for each month
        for ($m = 1; $m <= 12; $m++) {
            $monthTotal = POItem::join('pos', 'po_items.po_id', '=', 'pos.id')
                ->whereYear('pos.tanggal_po', $incYear)
                ->whereMonth('pos.tanggal_po', $m)
                ->sum('po_items.total');
                
            $revenueNetByMonthRaw[$m] = (int) $monthTotal;
        }
        $revenueNetByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueNetByMonth[$m] = (int) ($revenueNetByMonthRaw[$m] ?? 0);
        }

        // Detail per customer per bulan sepanjang tahun
        $revenueByCustomerByMonthRows = collect();
        
        // Get data for each month and customer
        for ($m = 1; $m <= 12; $m++) {
            $monthData = POItem::join('pos', 'po_items.po_id', '=', 'pos.id')
                ->whereYear('pos.tanggal_po', $incYear)
                ->whereMonth('pos.tanggal_po', $m)
                ->select('pos.customer', 
                    DB::raw('SUM(po_items.total) as subtotal'),
                    DB::raw("$m as bulan")
                )
                ->groupBy('pos.customer')
                ->orderBy('subtotal', 'desc')
                ->get();
                
            $revenueByCustomerByMonthRows = $revenueByCustomerByMonthRows->concat($monthData);
        }
        $revenueByCustomerByMonth = [];
        foreach ($revenueByCustomerByMonthRows as $row) {
            $b = (int) ($row->bulan ?? 0);
            if (!isset($revenueByCustomerByMonth[$b])) $revenueByCustomerByMonth[$b] = [];
            $revenueByCustomerByMonth[$b][] = [
                'customer' => $row->customer ?? '-',
                'subtotal' => (int) ($row->subtotal ?? 0),
            ];
        }

        return view('dashboard.finance_index', compact(
            'incMonth', 'incYear', 'allYears',
            'monthlySubtotal', 'monthlyPpn', 'monthlyRevenue',
            'revenueNetByMonth', 'revenueByCustomer', 'revenueByCustomerByMonth'
        ));
    }

    // Halaman Finance - Pengeluaran
    public function expense(Request $request)
    {
        $bulanNow = (int) $request->get('month', (int) Carbon::now()->format('n'));
        $tahunNow = (int) $request->get('year',  (int) Carbon::now()->format('Y'));
        
        // Array tahun untuk modal
        $allYears = range(2020, 2035);

        // Gaji per karyawan bulan/tahun dipilih
        $salaryByEmployee = Salary::with(['employee:id,nama_karyawan'])
            ->where('bulan', $bulanNow)
            ->where('tahun', $tahunNow)
            ->selectRaw('employee_id, SUM(total_gaji) as salary')
            ->groupBy('employee_id')
            ->get()
            ->map(function ($row) {
                return [
                    'employee' => $row->employee->nama_karyawan ?? '-',
                    'salary'   => (int) ($row->salary ?? 0),
                ];
            });

        $monthlySalaryTotal = (int) Salary::where('bulan', $bulanNow)
            ->where('tahun', $tahunNow)
            ->sum('total_gaji');

        // Pengeluaran lain bulan/tahun dipilih
        $otherExpensesMonthly = Expense::whereYear('tanggal', $tahunNow)
            ->whereMonth('tanggal', $bulanNow)
            ->orderByDesc('tanggal')
            ->get();
            
        $monthlyOtherExpenseTotal = (int) Expense::whereYear('tanggal', $tahunNow)
            ->whereMonth('tanggal', $bulanNow)
            ->sum('amount');

        // Rekap tahunan pengeluaran lain
        $expensesByMonthRaw = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTotal = Expense::whereYear('tanggal', $tahunNow)
                ->whereMonth('tanggal', $m)
                ->sum('amount');
            $expensesByMonthRaw[$m] = (int) $monthTotal;
        }
        $expensesByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $expensesByMonth[$m] = (int) ($expensesByMonthRaw[$m] ?? 0);
        }
        $yearlyOtherExpenseTotal = array_sum($expensesByMonth);

        // Rekap tahunan gaji
        $salaryByMonthRaw = Salary::where('tahun', $tahunNow)
            ->selectRaw('bulan, SUM(total_gaji) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');
        $salaryByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $salaryByMonth[$m] = (int) ($salaryByMonthRaw[$m] ?? 0);
        }
        $yearlySalaryTotal = array_sum($salaryByMonth);

        $combinedMonthlyExpense = (int) ($monthlySalaryTotal + $monthlyOtherExpenseTotal);
        $combinedYearlyExpenseTotal = (int) ($yearlySalaryTotal + $yearlyOtherExpenseTotal);

        return view('dashboard.finance_expense', compact(
            'bulanNow','tahunNow','allYears',
            'salaryByEmployee','monthlySalaryTotal','salaryByMonth','yearlySalaryTotal',
            'otherExpensesMonthly','monthlyOtherExpenseTotal','expensesByMonth','yearlyOtherExpenseTotal',
            'combinedMonthlyExpense','combinedYearlyExpenseTotal'
        ));
    }

    // API: Detail pendapatan per bulan (opsional filter per customer)
    public function incomeDetail(Request $request)
    {
        $month = (int) ($request->get('inc_month') ?? $request->get('month') ?? Carbon::now()->format('n'));
        $year  = (int) ($request->get('inc_year')  ?? $request->get('year')  ?? Carbon::now()->format('Y'));
        $customer = $request->get('customer');

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        // Ambil per PO: tanggal, nomor, customer, subtotal (net), ppn, bruto
        $query = DB::table('pos')
            ->leftJoin('po_items', 'po_items.po_id', '=', 'pos.id')
            ->whereBetween('pos.tanggal_po', [$start, $end])
            ->when($customer, fn($q)=> $q->where('pos.customer', $customer))
            ->groupBy('pos.id', 'pos.tanggal_po', 'pos.no_po', 'pos.customer')
            ->select(
                'pos.id as po_id',
                'pos.tanggal_po',
                'pos.no_po',
                'pos.customer',
                DB::raw('COALESCE(SUM(po_items.total),0) as net'),
                DB::raw('ROUND(COALESCE(SUM(po_items.total),0) * 0.11) as ppn'),
                DB::raw('COALESCE(SUM(po_items.total),0) + ROUND(COALESCE(SUM(po_items.total),0) * 0.11) as bruto')
            )
            ->orderByDesc('pos.tanggal_po');

        $rows = $query->get();
        // Lampirkan item pendapatan per PO agar detail lengkap
        $poIds = $rows->pluck('po_id')->all();
        $itemsByPo = collect();
        if (!empty($poIds)) {
            $items = DB::table('po_items')
                ->leftJoin('produks', 'produks.id', '=', 'po_items.produk_id')
                ->whereIn('po_items.po_id', $poIds)
                ->select(
                    'po_items.po_id',
                    'po_items.qty',
                    'po_items.qty_jenis',
                    'po_items.harga',
                    'po_items.total',
                    'produks.nama_produk',
                    'produks.satuan'
                )
                ->orderBy('po_items.id', 'asc')
                ->get()
                ->map(function($r){
                    // Normalisasi tipe numerik
                    $r->qty = (float) ($r->qty ?? 0);
                    $r->harga = (float) ($r->harga ?? 0);
                    $r->total = (float) ($r->total ?? 0);
                    return $r;
                })
                ->groupBy('po_id');
            $itemsByPo = $items;
        }

        $data = $rows->map(function($r) use ($itemsByPo){
            $r->items = array_values(($itemsByPo[$r->po_id] ?? collect())->toArray());
            return $r;
        });

        return response()->json([
            'month' => $month,
            'year' => $year,
            'customer' => $customer,
            'data' => $data,
        ]);
    }

    // API: Detail pengeluaran per bulan
    // type=salary => breakdown gaji per karyawan; type=other => pengeluaran lain
    public function expenseDetail(Request $request)
    {
        $month = (int) ($request->get('month') ?? Carbon::now()->format('n'));
        $year  = (int) ($request->get('year')  ?? Carbon::now()->format('Y'));
        $type  = $request->get('type', 'salary');

        if ($type === 'other') {
            $rows = Expense::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->orderByDesc('tanggal')
                ->get(['id','tanggal','jenis','deskripsi','amount']);
            return response()->json([
                'type' => 'other',
                'month' => $month,
                'year' => $year,
                'data' => $rows,
            ]);
        }

        // default salary
        $rows = Salary::with(['employee:id,nama_karyawan'])
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->orderBy('employee_id')
            ->get(['id','employee_id','total_gaji','tanggal_bayar','bulan','tahun']);

        $mapped = $rows->map(function($r){
            return [
                'id' => $r->id,
                // tanggal_bayar dapat bernilai null, kirim sebagai string Y-m-d atau null
                'tanggal_bayar' => $r->tanggal_bayar ? $r->tanggal_bayar->format('Y-m-d') : null,
                'bulan' => (int) ($r->bulan ?? 0),
                'tahun' => (int) ($r->tahun ?? 0),
                'employee' => $r->employee->nama_karyawan ?? '-',
                'total_gaji' => (int) $r->total_gaji,
            ];
        });

        return response()->json([
            'type' => 'salary',
            'month' => $month,
            'year' => $year,
            'data' => $mapped,
        ]);
    }

    // Store new expense
    public function storeExpense(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        Expense::create([
            'tanggal' => $request->tanggal,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            // Mirror ke kolom legacy 'description' jika ada di DB
            'description' => $request->deskripsi,
            'amount' => $request->amount,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil ditambahkan'
        ]);
    }
}
