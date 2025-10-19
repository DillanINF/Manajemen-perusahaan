<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'tunjangan',
        'status',
        'tanggal_masuk',
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

    public function scopeAktif($query)
    {
        if (Schema::hasColumn('employees', 'status')) {
            return $query->where('status', 'aktif');
        }
        return $query;
    }
}
