<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisaPOItem extends Model
{
    use HasFactory;

    protected $table = 'sisa_po_items';

    protected $fillable = [
        'no_po',
        'produk_id',
        'qty_diminta',
        'qty_tersedia',
        'qty_sisa',
        'qty_jenis',
        'harga',
        'total_sisa',
        'customer',
        'tanggal_po',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'harga' => 'decimal:2',
        'total_sisa' => 'decimal:2',
        'qty_diminta' => 'integer',
        'qty_tersedia' => 'integer',
        'qty_sisa' => 'integer',
    ];

    /**
     * Relasi ke tabel Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Scope untuk status pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk status fulfilled
     */
    public function scopeFulfilled($query)
    {
        return $query->where('status', 'fulfilled');
    }

    /**
     * Accessor untuk format total dengan mata uang
     */
    public function getFormattedTotalSisaAttribute()
    {
        return 'Rp ' . number_format($this->total_sisa, 0, ',', '.');
    }

    /**
     * Accessor untuk format tanggal PO
     */
    public function getFormattedTanggalPoAttribute()
    {
        return $this->tanggal_po ? $this->tanggal_po->format('d/m/Y') : null;
    }
}
