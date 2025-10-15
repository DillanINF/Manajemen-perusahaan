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
        'gaji_pokok',
        'total_gaji',
        'status_pembayaran'
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'gaji_pokok' => 'integer',
        'total_gaji' => 'integer'
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
}
