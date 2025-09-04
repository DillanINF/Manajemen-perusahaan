<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JatuhTempo extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'no_po',
        'customer',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'jumlah_tagihan',
        'jumlah_terbayar',
        'sisa_tagihan',
        'status_pembayaran',
        // Approval status: Pending | ACC | Reject
        'status_approval',
        'hari_terlambat',
        'denda',
        'catatan',
        'reminder_sent',
        'last_reminder_date'
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'last_reminder_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    // Accessor untuk warna status
    public function getStatusColorAttribute()
    {
        switch ($this->status_pembayaran) {
            case 'Accept':
                return 'green';
            case 'Pending':
                return 'yellow';
            default:
                return 'gray';
        }
    }
}
