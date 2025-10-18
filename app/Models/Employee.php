<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_karyawan',
        'name',
        'email',
        'no_telepon',
        'alamat',
        'posisi',
        'departemen',
        'gaji_pokok',
        'status',
        'foto'
    ];

    protected $casts = [
        // gaji_pokok cast removed to prevent errors with null/0 values
    ];

    // Accessor untuk total gaji
    public function getTotalGajiAttribute()
    {
        return $this->gaji_pokok;
    }

    // Scope untuk karyawan aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk karyawan tidak aktif
    public function scopeTidakAktif($query)
    {
        return $query->where('status', 'tidak_aktif');
    }
}
