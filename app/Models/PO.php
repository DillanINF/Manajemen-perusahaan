<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PO extends Model
{
    use HasFactory;

    protected $table = 'pos';

    protected $fillable = [
        'tanggal_po',
        'customer_id',
        'customer',
        'pengirim',       // kolom penghubung ke tabel pengirim
        'no_surat_jalan',
        'no_po',
        'po_number',
        'no_invoice',
        'no_polisi',
        'kendaraan',      // ID kendaraan
        'qty',
        'qty_jenis',
        'produk_id',      // ID produk
        'harga',
        'total',
        'alamat_1',
        'alamat_2',
        'status_approval',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'customer_id' => 'integer',
        'qty'        => 'integer',
        'qty_jenis'  => 'string',   // satuan (PCS, KG, DLL)
        'harga'      => 'decimal:2',
        'total'      => 'decimal:2',
        'status_approval' => 'string',
    ];

    /**
     * Relasi ke tabel Produk
     */
    public function produkRel()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }

    /**
     * Relasi ke tabel Surat Jalan
     */
    public function suratJalan()
    {
        return $this->hasOne(SuratJalan::class, 'no_surat_jalan', 'no_surat_jalan');
    }

    /**
     * Relasi ke tabel Kendaraan
     */
    public function kendaraanRel()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan', 'id');
    }

    /**
     * Relasi ke tabel Customer
     */
    public function customerRel()
    {
        return $this->belongsTo(Customer::class, 'customer', 'nama');
    }

    /**
     * Relasi ke tabel Pengirim
     * pos.pengirim = pengirim.nama
     */
    public function pengirimRel()
    {
        return $this->belongsTo(Pengirim::class, 'pengirim', 'nama');
    }

    /**
     * Relasi ke tabel POItem (detail item per PO)
     */
    public function items()
    {
        return $this->hasMany(POItem::class, 'po_id');
    }

    /**
     * Accessor untuk format tanggal PO
     */
    public function getFormattedTanggalPoAttribute()
    {
        return $this->tanggal_po ? $this->tanggal_po->format('d/m/Y') : null;
    }

    /**
     * Accessor untuk format total dengan mata uang
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}
