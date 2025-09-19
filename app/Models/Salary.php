<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'jenis_gaji',
        'jumlah_hari',
        'tarif_harian',
        'jumlah_unit',
        'tarif_per_unit',
        'bulan',
        'tahun',
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
        'tanggal_bayar' => 'date',
        'gaji_pokok' => 'integer',
        'jumlah_hari' => 'integer',
        'tarif_harian' => 'integer',
        'jumlah_unit' => 'integer',
        'tarif_per_unit' => 'integer',
        'tunjangan' => 'integer',
        'bonus' => 'integer',
        'lembur' => 'integer',
        'potongan_pajak' => 'integer',
        'potongan_bpjs' => 'integer',
        'potongan_lain' => 'integer',
        'total_gaji' => 'integer'
    ];

    // Relationship dengan Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessor untuk total pendapatan
    public function getTotalPendapatanAttribute()
    {
        return $this->gaji_pokok + $this->tunjangan + $this->bonus + $this->lembur;
    }

    // Accessor untuk total potongan
    public function getTotalPotonganAttribute()
    {
        return $this->potongan_pajak + $this->potongan_bpjs + $this->potongan_lain;
    }

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
