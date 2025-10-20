<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MODEL TIDAK DIGUNAKAN - TABEL INVOICES SUDAH DIHAPUS
 * Data invoice sekarang disimpan di tabel POS
 * Model ini di-keep agar tidak error di InvoiceController yang sudah dinonaktifkan
 */
class Invoice extends Model
{
    use HasFactory;

    // Nonaktifkan semua interaksi dengan database
    protected $table = 'pos'; // Redirect ke tabel POS untuk menghindari error
    
    protected $fillable = [
        'no_invoice',
        'no_po',
        'customer',
        'tanggal_invoice',
        'qty',
        'qty_jenis',
        'total',
        'grand_total',
        'status'
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
    ];

    // Nonaktifkan event
    // protected static function booted() {}

    public function produkRel()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
