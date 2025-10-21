<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'tahun',
        'jenis_gaji',
        'jumlah_hari',
        'tarif_harian',
        'jumlah_unit',
        'tarif_per_unit',
        'gaji_pokok',
        'tunjangan',
        'bonus',
        'lembur',
        'potongan_pajak',
        'potongan_bpjs',
        'potongan_lain',
        'total_gaji',
        'status_pembayaran',
        'tanggal_bayar',
        'keterangan'
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'jumlah_hari' => 'integer',
        'tarif_harian' => 'integer',
        'jumlah_unit' => 'integer',
        'tarif_per_unit' => 'integer',
        'gaji_pokok' => 'float',
        'tunjangan' => 'float',
        'bonus' => 'float',
        'lembur' => 'float',
        'potongan_pajak' => 'float',
        'potongan_bpjs' => 'float',
        'potongan_lain' => 'float',
        'total_gaji' => 'float',
        'tanggal_bayar' => 'date'
    ];

    // Scope untuk gaji yang sudah dibayar
    public function scopeDibayar($query)
    {
        return $query->where('status_pembayaran', 'dibayar');
    }

    // Scope untuk gaji yang belum dibayar
    public function scopeBelumDibayar($query)
    {
        return $query->where('status_pembayaran', 'belum_dibayar');
    }

    // Scope untuk bulan dan tahun tertentu
    public function scopeBulanTahun($query, $bulan, $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    // Relasi ke Employee - DIHAPUS karena kolom employee_id sudah tidak ada
}
