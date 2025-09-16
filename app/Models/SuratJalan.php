<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'pos';

    protected $fillable = [
        'tanggal_po',
        'customer',
        'alamat_1',
        'alamat_2',
        'no_surat_jalan',
        'no_po',
        'po_number',
        'kendaraan',
        'no_polisi',
        'qty',
        'qty_jenis',
        'produk_id',
        'harga',
        'total',
        'pengirim',
        'no_invoice',
        'status_approval'
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'qty' => 'integer',
        'harga' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relasi ke Produk
    public function produkRel()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Relasi ke Kendaraan
    public function kendaraanRel()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan', 'nama');
    }

    // Relasi ke Customer
    public function customerRel()
    {
        return $this->belongsTo(Customer::class, 'customer', 'name');
    }
}
