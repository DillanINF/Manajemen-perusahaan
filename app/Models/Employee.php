<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Employee extends Model
{
    use HasFactory;

    // Hanya 4 kolom yang tersisa di database
    protected $fillable = [
        'nama_karyawan',
        'no_telepon',
        'alamat',
        'posisi',
    ];
}
