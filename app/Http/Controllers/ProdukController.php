<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProdukController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        // Eager-load total qty masuk & keluar agar perhitungan sisa stok efisien
        $produks = Produk::query()
            ->withSum('barangMasuks as qty_masuk', 'qty')
            ->withSum('barangKeluars as qty_keluar', 'qty')
            ->get();
        return view('produk.index', compact('produks'));
    }

    /**
     * Menampilkan form input produk.
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Simpan data produk baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kode_produk' => 'nullable|string|max:50|unique:produks,kode_produk',
                'nama_produk' => 'required|string|max:255',
                'harga' => 'nullable|numeric|min:0',
                'harga_pcs' => 'nullable|numeric|min:0',
                'harga_set' => 'nullable|numeric|min:0',
            ]);

            // Pastikan kode_produk selalu ada
            if (empty($validated['kode_produk'])) {
                $validated['kode_produk'] = $this->generateKodeProduk();
            }

            // Set harga final berdasarkan input yang tersedia (prioritaskan SET jika ada)
            if (empty($validated['harga'])) {
                if (!empty($validated['harga_set']) && (float)$validated['harga_set'] > 0) {
                    $validated['harga'] = $validated['harga_set'];
                } elseif (!empty($validated['harga_pcs']) && (float)$validated['harga_pcs'] > 0) {
                    $validated['harga'] = $validated['harga_pcs'];
                } else {
                    $validated['harga'] = 0;
                }
            }

            // Set default untuk field yang kosong
            $validated['harga_pcs'] = $validated['harga_pcs'] ?? 0;
            $validated['harga_set'] = $validated['harga_set'] ?? 0;

            // Jaga kompatibilitas: jika kolom 'name' di DB wajib isi, mirror dari nama_produk
            if (empty($validated['name']) && !empty($validated['nama_produk'])) {
                $validated['name'] = $validated['nama_produk'];
            }

            // Log untuk debugging
            Log::info('Creating produk with data:', $validated);

            $produk = Produk::create($validated);

            // Cek apakah request ini dari AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'produk' => $produk,
                    'message' => 'Data produk berhasil ditambahkan.'
                ]);
            }

            return redirect()->route('produk.index')
                             ->with('success', 'Data produk berhasil ditambahkan.');

        } catch (ValidationException $e) {
            // Kembalikan error validasi agar ditampilkan oleh blade ($errors)
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating produk: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk edit produk.
     */
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return view('produk.edit', compact('produk'));
    }

    /**
     * Update data produk.
     */
    public function update(Request $request, Produk $produk)
    {
        try {
            $validated = $request->validate([
                'kode_produk' => 'nullable|string|max:50|unique:produks,kode_produk,' . $produk->id,
                'nama_produk' => 'required|string|max:255',
                'harga' => 'nullable|numeric|min:0',
                'harga_pcs' => 'nullable|numeric|min:0',
                'harga_set' => 'nullable|numeric|min:0',
            ]);

            // Generate kode_produk otomatis jika kosong
            if (empty($validated['kode_produk'])) {
                $validated['kode_produk'] = $this->generateKodeProduk();
            }

            // Set harga final berdasarkan input yang tersedia (prioritaskan SET jika ada)
            if (empty($validated['harga'])) {
                if (!empty($validated['harga_set']) && (float)$validated['harga_set'] > 0) {
                    $validated['harga'] = $validated['harga_set'];
                } elseif (!empty($validated['harga_pcs']) && (float)$validated['harga_pcs'] > 0) {
                    $validated['harga'] = $validated['harga_pcs'];
                } else {
                    $validated['harga'] = 0;
                }
            }

            // Set default untuk field yang kosong
            $validated['harga_pcs'] = $validated['harga_pcs'] ?? 0;
            $validated['harga_set'] = $validated['harga_set'] ?? 0;

            $produk->update($validated);

            // Cek AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'produk' => $produk,
                    'message' => 'Data produk berhasil diupdate.'
                ]);
            }

            return redirect()->route('produk.index')
                             ->with('success', 'Data produk berhasil diupdate.');

        } catch (\Exception $e) {
            Log::error('Error updating produk: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate produk: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal mengupdate produk: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk dari database.
     */
    public function destroy(Produk $produk)
    {
        try {
            $produk->delete();

            // Cek AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data produk berhasil dihapus.'
                ]);
            }

            return redirect()->route('produk.index')
                             ->with('success', 'Data produk berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting produk: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus produk: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('produk.index')
                             ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Generate kode produk otomatis dengan logic yang lebih robust
     */
    private function generateKodeProduk()
    {
        try {
            $prefix = 'PRD';
            
            // Gunakan transaction untuk memastikan konsistensi
            return DB::transaction(function () use ($prefix) {
                $lastProduk = Produk::where('kode_produk', 'like', $prefix . '%')
                                   ->orderByRaw('CAST(SUBSTRING(kode_produk, 4) AS UNSIGNED) DESC')
                                   ->lockForUpdate()
                                   ->first();
                
                if ($lastProduk && preg_match('/^' . $prefix . '(\d+)$/', $lastProduk->kode_produk, $matches)) {
                    $lastNumber = (int) $matches[1];
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                
                $newKode = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                
                // Double check untuk memastikan kode belum ada
                while (Produk::where('kode_produk', $newKode)->exists()) {
                    $newNumber++;
                    $newKode = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                }
                
                return $newKode;
            });

        } catch (\Exception $e) {
            Log::error('Error generating kode produk: ' . $e->getMessage());
            // Fallback dengan timestamp jika gagal
            return 'PRD' . date('YmdHis');
        }
    }

    /**
     * Show specific produk
     */
    public function show(Produk $produk)
    {
        return view('produk.show', compact('produk'));
    }
}