<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PO;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\JatuhTempo;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['produkRel'])->latest()->get();
        $pos = PO::all();
        $produks = Produk::all();
        $customers = Customer::all();

        return view('dashboard.invoice_index', compact('invoices', 'pos', 'produks', 'customers'));
    }

    public function create()
    {
        $pos = PO::all();
        $produks = Produk::all();
        $customers = Customer::all();

        return view('dashboard.invoice_create', compact('pos', 'produks', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'no_invoice' => 'required|string|unique:invoices',
            'no_po' => 'required|string',
            'customer' => 'required|string',
            'alamat_1' => 'nullable|string',
            'alamat_2' => 'nullable|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'produk_id' => 'required|exists:produks,id',
            'qty' => 'required|integer|min:1',
            'qty_jenis' => 'required|in:PCS,SET',
            'harga' => 'required|integer',
            'total' => 'required|integer',
            'pajak' => 'nullable|integer',
            'grand_total' => 'required|integer',
            'status' => 'required|in:Draft,Sent,Paid,Overdue',
            'keterangan' => 'nullable|string'
        ]);

        Invoice::create($data);

        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoices = Invoice::with(['produkRel'])->latest()->get();
        $pos = PO::all();
        $produks = Produk::all();
        $customers = Customer::all();

        return view('dashboard.invoice_index', compact('invoice', 'invoices', 'pos', 'produks', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'no_invoice' => 'required|string|unique:invoices,no_invoice,' . $invoice->id,
            'no_po' => 'required|string',
            'customer' => 'required|string',
            'alamat_1' => 'nullable|string',
            'alamat_2' => 'nullable|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'produk_id' => 'required|exists:produks,id',
            'qty' => 'required|integer|min:1',
            'qty_jenis' => 'required|in:PCS,SET',
            'harga' => 'required|integer',
            'total' => 'required|integer',
            'pajak' => 'nullable|integer',
            'grand_total' => 'required|integer',
            'status' => 'required|in:Draft,Sent,Paid,Overdue',
            'keterangan' => 'nullable|string'
        ]);

        $invoice->update($data);

        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        // Hapus semua entri Jatuh Tempo yang terkait berdasarkan no_invoice maupun no_po
        try {
            if (!empty($invoice->no_invoice)) {
                JatuhTempo::where('no_invoice', $invoice->no_invoice)->delete();
            }
            if (!empty($invoice->no_po)) {
                JatuhTempo::where('no_po', $invoice->no_po)->delete();
            }
        } catch (\Throwable $e) {
            \Log::warning('[JT] Gagal menghapus entri Jatuh Tempo terkait invoice', [
                'error' => $e->getMessage(),
                'no_invoice' => $invoice->no_invoice,
                'no_po' => $invoice->no_po,
            ]);
        }

        $invoice->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice dan data Jatuh Tempo terkait berhasil dihapus.');
    }

    // HAPUS SEMUA DATA INVOICE (+ JATUH TEMPO TERKAIT)
    public function destroyAll()
    {
        try {
            // Hapus semua Jatuh Tempo terlebih dahulu agar tidak ada sisa relasi
            JatuhTempo::query()->delete();
            // Hapus semua invoice
            Invoice::query()->delete();
            return redirect()->route('invoice.index')->with('success', 'Semua data Invoice dan Jatuh Tempo terkait berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->route('invoice.index')->with('error', 'Gagal menghapus semua data Invoice: ' . $e->getMessage());
        }
    }
}
