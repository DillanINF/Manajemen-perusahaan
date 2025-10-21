<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produk;
use App\Models\PO;
use App\Models\SisaPOItem;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;

class SisaDataPOController extends Controller
{
    /**
     * Tampilkan halaman sisa data purchase order
     */
    public function index(Request $request)
    {
        // Ambil filter dari request
        $noPo = $request->get('no_po');
        $produkId = $request->get('produk_id');
        $customer = $request->get('customer');
        
        // Query untuk mendapatkan sisa data PO
        $sisaDataQuery = $this->getSisaDataPOQuery($noPo, $produkId, $customer);
        
        // Ambil data dengan pagination
        $sisaData = $sisaDataQuery->paginate(15);
        
        // Ambil semua produk untuk filter
        $produks = Produk::orderBy('nama_produk')->get();
        
        // Ambil daftar customer unik untuk suggestion pencarian
        $customers = DB::table('sisa_po_items')
            ->select('customer')
            ->whereNotNull('customer')
            ->where('customer', '!=', '')
            ->where('customer', '!=', '-')
            ->where('status', 'pending')
            ->distinct()
            ->orderBy('customer')
            ->pluck('customer');

        return view('po.sisa_data', compact('sisaData', 'produks', 'noPo', 'produkId', 'customer', 'customers'));
    }
    
    /**
     * Query untuk mendapatkan sisa data PO
     */
    private function getSisaDataPOQuery($noPo = null, $produkId = null, $customer = null)
    {
        // Query langsung dari tabel sisa_po_items yang menyimpan data split yang akurat
        $query = DB::table('sisa_po_items')
            ->join('produks', 'sisa_po_items.produk_id', '=', 'produks.id')
            ->select(
                'sisa_po_items.id as sisa_id',
                'sisa_po_items.no_po',
                'sisa_po_items.produk_id',
                'produks.nama_produk',
                'produks.satuan',
                'sisa_po_items.qty_diminta as total_qty_po',
                'sisa_po_items.qty_tersedia as sisa_stok',
                'sisa_po_items.qty_sisa as sisa_belum_terinput',
                'sisa_po_items.harga',
                'sisa_po_items.customer',
                DB::raw('CASE 
                    WHEN sisa_po_items.harga > 0 THEN "Berbayar"
                    ELSE "Gratis"
                END as tipe_harga')
            )
            ->where('sisa_po_items.status', 'pending')
            ->orderBy('sisa_po_items.no_po')
            ->orderBy('produks.nama_produk');
        
        // Apply filters
        if ($noPo) {
            $query->where('sisa_po_items.no_po', 'LIKE', "%{$noPo}%");
        }

        if ($produkId) {
            $query->where('sisa_po_items.produk_id', $produkId);
        }
        
        if ($customer) {
            $query->where('sisa_po_items.customer', 'LIKE', "%{$customer}%");
        }

        return $query;
    }
    
    /**
     * Export data sisa PO ke Excel
     */
    public function export(Request $request)
    {
        $noPo = $request->get('no_po');
        $produkId = $request->get('produk_id');
        $customer = $request->get('customer');

        $sisaData = $this->getSisaDataPOQuery($noPo, $produkId, $customer)->get();
        
        return response()->streamDownload(function () use ($sisaData) {
            $handle = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($handle, [
                'No PO',
                'Nama Produk',
                'Satuan',
                'Qty PO',
                'Stok Masuk',
                'Stok Keluar',
                'Sisa Stok',
                'Sisa Belum Terinput'
            ]);
            
            // Data rows
            foreach ($sisaData as $item) {
                fputcsv($handle, [
                    $item->no_po,
                    $item->nama_produk,
                    $item->satuan,
                    $item->total_qty_po,
                    $item->total_stok_masuk,
                    $item->total_stok_keluar,
                    $item->sisa_stok,
                    $item->sisa_belum_terinput
                ]);
            }
            
            fclose($handle);
        }, 'sisa-data-po-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sisa-data-po-' . date('Y-m-d') . '.csv"',
        ]);
    }
    
    /**
     * API untuk mendapatkan detail sisa data berdasarkan no_po dan produk_id
     */
    public function getDetail($id)
    {
        try {
            $sisaData = $this->getSisaDataPOQuery()
                ->where('sisa_po_items.produk_id', $id)
                ->first();
            
            if (!$sisaData) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
            
            return response()->json($sisaData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Edit sisa data PO
     */
    public function edit($id)
    {
        $sisaItem = SisaPOItem::with('produk')->findOrFail($id);
        $produks = Produk::orderBy('nama_produk')->get();
        
        return view('po.sisa_data_edit', compact('sisaItem', 'produks'));
    }

    /**
     * Update sisa data PO
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'produk_id' => 'required|integer|exists:produks,id',
            'qty_sisa' => 'required|integer|min:1',
        ]);

        $sisaItem = SisaPOItem::findOrFail($id);
        $sisaItem->update([
            'produk_id' => $validated['produk_id'],
            'qty_sisa' => $validated['qty_sisa'],
        ]);

        return redirect()->route('sisa-data-po.index')
            ->with('success', 'Sisa data PO berhasil diperbarui.');
    }

    /**
     * Delete sisa data PO
     */
    public function destroy($id)
    {
        $sisaItem = SisaPOItem::findOrFail($id);
        $sisaItem->delete();

        return redirect()->route('sisa-data-po.index')
            ->with('success', 'Sisa data PO berhasil dihapus.');
    }
}
