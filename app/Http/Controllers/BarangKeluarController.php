<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BarangKeluar;
use App\Models\Produk;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customer = trim((string) $request->query('customer', ''));
        $date = $request->query('date');

        $query = BarangKeluar::query()
            ->with('produk')
            // Join ke POS berdasarkan pola keterangan standar
            // Perkuat matching: TRIM dan LIKE untuk variasi spasi/format
            ->leftJoin('pos', function($join) {
                $join->on(DB::raw("TRIM(barang_keluars.keterangan)"), '=', DB::raw("TRIM(CONCAT('Auto Keluar dari PO ', pos.no_po))"))
                     ->orOn(DB::raw("TRIM(barang_keluars.keterangan)"), 'LIKE', DB::raw("CONCAT('%PO ', TRIM(pos.no_po), '%')"));
            })
            ->select('barang_keluars.*', DB::raw('pos.customer as customer_name'));

        if ($customer !== '') {
            $query->where(function($q) use ($customer) {
                $q->where('pos.customer', 'like', "%$customer%")
                  ->orWhere('barang_keluars.keterangan', 'like', "%$customer%");
            });
        }
        if (!empty($date)) {
            $query->whereDate('barang_keluars.tanggal', '=', $date);
        }

        $items = $query->latest('barang_keluars.tanggal')->paginate(15)->withQueryString();

        return view('barang.keluar.index', compact('items', 'customer', 'date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Input manual dinonaktifkan: barang keluar dibuat otomatis dari PO
        return redirect()->route('barang.keluar.index')
            ->with('error', 'Input manual Barang Keluar dinonaktifkan. Data berasal otomatis dari PO.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(403, 'Input manual Barang Keluar dinonaktifkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BarangKeluar $keluar)
    {
        abort(403, 'Edit Barang Keluar dinonaktifkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarangKeluar $keluar)
    {
        abort(403, 'Update Barang Keluar dinonaktifkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangKeluar $keluar)
    {
        $keluar->delete();
        return redirect()->route('barang.keluar.index')->with('success', 'Data barang keluar berhasil dihapus');
    }
}
