<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimController extends Controller
{
    public function index()
    {
        $pengirims = DB::table('pengirim')
            ->select('id', 'nama', 'kendaraan', 'no_polisi')
            ->orderBy('nama', 'asc')
            ->get();
        
        return view('pengirim.index', compact('pengirims'));
    }

    public function store(Request $request)
    {
        // NOTE: Nama pengirim BOLEH DUPLIKAT (tidak ada validasi unique)
        // Ini memungkinkan input pengirim dengan nama yang sama berkali-kali
        $request->validate([
            'nama' => 'required|string|max:255',
            'kendaraan' => 'nullable|string|max:255',
            'no_polisi' => 'nullable|string|max:50',
        ]);

        DB::table('pengirim')->insert([
            'nama' => $request->nama,
            'kendaraan' => $request->kendaraan,
            'no_polisi' => $request->no_polisi,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('pengirim.index')->with('success', 'Data pengirim berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kendaraan' => 'nullable|string|max:255',
            'no_polisi' => 'nullable|string|max:50',
        ]);

        DB::table('pengirim')
            ->where('id', $id)
            ->update([
                'nama' => $request->nama,
                'kendaraan' => $request->kendaraan,
                'no_polisi' => $request->no_polisi,
                'updated_at' => now()
            ]);

        return redirect()->route('pengirim.index')->with('success', 'Data pengirim berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('pengirim')->where('id', $id)->delete();

        return redirect()->route('pengirim.index')->with('success', 'Data pengirim berhasil dihapus');
    }
}
