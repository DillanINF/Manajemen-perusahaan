<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // Nama tabel sesuai database
    protected $table = 'produks';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'name',
        'harga',
        'harga_pcs',
        'harga_set',
    ];

    // Otomatis tambahkan atribut sisa_stok ke serialization
    protected $appends = ['sisa_stok'];

    /**
     * Relasi ke Pos
     * Satu produk bisa digunakan di banyak surat jalan / PO
     */
    public function pos()
    {
        return $this->hasMany(Pos::class, 'produk_id');
    }

    /**
     * Relasi stok: Barang Masuk dan Barang Keluar
     */
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'produk_id');
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class, 'produk_id');
    }

    /**
     * Accessor sisa_stok = total qty_masuk - qty_keluar
     * Menggunakan nilai withSum jika sudah di-load untuk efisiensi
     */
    public function getSisaStokAttribute(): int
    {
        $masuk = $this->getAttribute('qty_masuk') ?? $this->barangMasuks()->sum('qty');
        $keluar = $this->getAttribute('qty_keluar') ?? $this->barangKeluars()->sum('qty');
        return (int) ($masuk - $keluar);
    }
}
