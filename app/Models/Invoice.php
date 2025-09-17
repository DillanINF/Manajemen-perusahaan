<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\JatuhTempo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'no_po',
        'customer',
        'alamat_1',
        'alamat_2',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'produk_id',
        'qty',
        'qty_jenis',
        'harga',
        'total',
        'pajak',
        'grand_total',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    protected static function booted()
    {
        static::deleting(function (Invoice $invoice) {
            try {
                // Hapus semua Jatuh Tempo yang terkait via no_invoice dan no_po
                $noInv = trim((string)($invoice->no_invoice ?? ''));
                $noPo  = trim((string)($invoice->no_po ?? ''));

                if ($noInv !== '') {
                    // Match persis dan TRIM
                    JatuhTempo::where('no_invoice', $noInv)->delete();
                    JatuhTempo::whereRaw('TRIM(no_invoice) = ?', [$noInv])->delete();
                    // Jika numerik, coba hapus berdasarkan cast integer juga
                    if (is_numeric($noInv)) {
                        $noInvInt = (string) ((int) $noInv);
                        JatuhTempo::where('no_invoice', $noInvInt)->delete();
                        JatuhTempo::whereRaw('TRIM(no_invoice) = ?', [$noInvInt])->delete();
                    }
                }

                if ($noPo !== '' && $noPo !== '-') {
                    JatuhTempo::where('no_po', $noPo)->delete();
                    JatuhTempo::whereRaw('TRIM(no_po) = ?', [$noPo])->delete();
                }
            } catch (\Throwable $e) {
                Log::warning('[Invoice model] Gagal cascade delete JatuhTempo saat menghapus invoice', [
                    'error' => $e->getMessage(),
                    'no_invoice' => $invoice->no_invoice,
                    'no_po' => $invoice->no_po,
                ]);
            }
        });
    }

    public function produkRel()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
