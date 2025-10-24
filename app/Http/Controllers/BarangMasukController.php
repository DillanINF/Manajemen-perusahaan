<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Produk;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = BarangMasuk::with('produk')
            ->latest('tanggal')
            ->paginate(10);

        return view('barang.masuk.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $produks = Produk::query()
            ->withSum('barangMasuks as qty_masuk', 'qty')
            ->withSum('barangKeluars as qty_keluar', 'qty')
            ->orderBy('nama_produk')
            ->get();
        $selectedProdukId = (int) $request->query('produk_id', 0);
        return view('barang.masuk.create', compact('produks', 'selectedProdukId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'produk_id' => ['required','exists:produks,id'],
            'qty' => ['required','integer','min:1'],
            'tanggal' => ['required','date'],
            'keterangan' => ['nullable','string'],
        ]);

        $data['user_id'] = auth()->id();

        BarangMasuk::create($data);

        // Jika AJAX request, kembalikan JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Barang Masuk berhasil ditambahkan.'
            ]);
        }

        return redirect()->route('barang.masuk.index')->with('success', 'Barang Masuk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BarangMasuk $masuk)
    {
        $produks = Produk::orderBy('nama_produk')->get();
        return view('barang.masuk.edit', compact('masuk','produks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarangMasuk $masuk)
    {
        $data = $request->validate([
            'produk_id' => ['required','exists:produks,id'],
            'nama_produk' => ['required','string','max:255'],
            'qty' => ['required','integer','min:1'],
            'tanggal' => ['required','date'],
            'keterangan' => ['nullable','string'],
        ]);

        // Jika nama produk diubah, cari apakah sudah ada produk lain dengan nama itu
        $targetName = trim($data['nama_produk']);
        $currentProduk = \App\Models\Produk::find($data['produk_id']);
        $existing = \App\Models\Produk::where('nama_produk', $targetName)->first();

        if ($existing) {
            // Relink ke produk yang sudah ada
            $data['produk_id'] = $existing->id;
        } else {
            // Update nama pada produk saat ini
            if ($currentProduk && $currentProduk->nama_produk !== $targetName) {
                $currentProduk->nama_produk = $targetName;
                $currentProduk->save();
            }
        }

        unset($data['nama_produk']);

        $masuk->update($data);

        return redirect()->route('barang.masuk.index')->with('success', 'Barang Masuk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangMasuk $masuk)
    {
        $masuk->delete();
        return redirect()->route('barang.masuk.index')->with('success', 'Barang Masuk berhasil dihapus.');
    }
}
