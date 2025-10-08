<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice PDF</title>
    @php
        $items = $invoiceDetails['items'] ?? [];
        $itemCount = is_countable($items) ? count($items) : 0;
        // Banyak baris yang akan dirender (minimal 20 sesuai keinginan)
        $renderCount = max($itemCount, 20);
        // Mode skala berdasarkan renderCount agar 20 baris tetap muat 1 halaman
        if ($renderCount <= 14) {
            $mode = 'normal';
        } elseif ($renderCount <= 20) {
            $mode = 'compact';
        } else {
            $mode = 'ultra';
        }

        // Skala visual per mode (lebih kecil untuk compact/ultra)
        if ($mode === 'normal') {
            $fsBase = 11.2; $padCell = 6;   $padBox = 8;  $mb8 = 12; $mb4 = 8;
            $hLogo = 64;    $hStamp = 68;  $titleFs = 30; $addrFs = 9.2;
            $pageTop = 12;  $pageBottom = 10; $tdRightFs = 10.2; $lineHeight = 1.22;
        } elseif ($mode === 'compact') {
            // Dipakai untuk renderCount <= 20 (target 1 halaman dengan 20 baris)
            $fsBase = 8.8;  $padCell = 2.4; $padBox = 4;  $mb8 = 6;  $mb4 = 4;
            $hLogo = 52;    $hStamp = 56;  $titleFs = 22; $addrFs = 8.4;
            $pageTop = 7;   $pageBottom = 6;  $tdRightFs = 9.0;  $lineHeight = 1.05;
        } else { // ultra (>=21 baris)
            $fsBase = 8.2;  $padCell = 2.2; $padBox = 4;  $mb8 = 6;  $mb4 = 3;
            $hLogo = 46;    $hStamp = 52;  $titleFs = 19; $addrFs = 8.0;
            $pageTop = 6;   $pageBottom = 6;  $tdRightFs = 8.8;  $lineHeight = 1.04;
        }
        $pageSide = 10; // left/right margin constant (mm)
        // Lebar kolom dinamis (sisanya otomatis untuk DESCRIPTION)
        $wQty = $mode === 'normal' ? '15%' : ($mode === 'compact' ? '13%' : '12%');
        $wUnit = $mode === 'normal' ? '20%' : ($mode === 'compact' ? '18%' : '16%');
        $wAmt = $mode === 'normal' ? '20%' : ($mode === 'compact' ? '18%' : '16%');
    @endphp
    <style>
        /* Force exact A4 page with controlled margins in DomPDF */
        @page { size: A4 portrait; margin: {{ $pageTop }}mm {{ $pageSide }}mm {{ $pageBottom }}mm {{ $pageSide }}mm; }
        html, body { margin: 0; padding: 0; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: {{ $fsBase }}px; color: #000; }
        /* Center the page content while respecting @page margins */
        .page { width: 190mm; min-height: auto; padding: 4mm 0 0 0; margin: 0 auto; background: #fff; }
        .row { display: flex; }
        .between { justify-content: space-between; }
        .mb-8 { margin-bottom: {{ $mb8 }}px; }
        .mb-4 { margin-bottom: {{ $mb4 }}px; }
        .border { border: 1px solid #000; }
        .p-2 { padding: {{ $padBox }}px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: {{ $padCell }}px; page-break-inside: avoid; line-height: {{ $lineHeight }}; }
        th { text-align: center; }
        .no-border { border: 0; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        .no-break { page-break-inside: avoid; }
        .col-desc { word-wrap: break-word; word-break: break-word; white-space: normal; }
    </style>
</head>
<body>
<div class="page">
    @php
        try {
            $logoFile = public_path('image/LOGO.png');
            $logoData = file_exists($logoFile) ? base64_encode(file_get_contents($logoFile)) : null;
        } catch (\Throwable $e) { $logoData = null; }
    @endphp
    <div class="row mb-8 no-break" style="align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: {{ max(6, $padBox - 2) }}px;">
        <img src="{{ $logoData ? ('data:image/png;base64,'.$logoData) : asset('image/LOGO.png') }}" alt="Logo" style="height:{{ $hLogo }}px;width:auto;object-fit:contain;margin-right:16px;">
        <div style="flex: 1;">
            <h2 style="margin:0; font-size:16px; font-weight:bold; color:#d32f2f;">PT. CAM JAYA ABADI</h2>
            <p style="margin:2px 0; font-size:{{ $addrFs }}px; line-height:1.2; color: rgb(38,73,186);">
                <strong>MANUFACTURING PROFESSIONAL WOODEN PALLET</strong><br>
                <strong>KILN DRYING WOOD WORKING INDUSTRY</strong><br>
                Factory & Office : Jl. Wahana Bakti No.28, Mangunjaya, Kec. Tambun Sel. Bekasi Jawa Barat<br>
                17510<br>
                Telp: (021) 6617 1626 - Fax: (021) 6617 3986
            </p>
        </div>
    </div>

    <div class="mb-4">
        <div class="border p-2">
            <strong>Kepada Yth.</strong><br>
            <span class="bold">{{ $invoiceDetails['customer'] ?? '-' }}</span><br>
            di .<br>
            @php
                $rawAddress = $invoiceDetails['address'] ?? '-';
                if (is_string($rawAddress)) {
                    // Pastikan setelah koma selalu ada spasi dan hilangkan spasi berlebih
                    $addr = preg_replace('/,\s*/', ', ', $rawAddress); // koma diikuti satu spasi
                    $addr = preg_replace('/\s+/', ' ', $addr);          // kompres spasi ganda
                    $addr = trim($addr, " \t\n\r\0\x0B, ");         // trim spasi/koma tak perlu di ujung
                } else {
                    $addr = '-';
                }
            @endphp
            <b>{{ $addr }}</b>
        </div>
    </div>

    <div class="text-center mb-4 no-break">
        <h1 style="font-size: {{ $titleFs }}px; font-weight: bold; letter-spacing: 3px; margin: 0; color:#333;">INVOICE</h1>
    </div>

    @php
        // Deteksi multi-PO untuk mengosongkan No. PO di baris info jika lebih dari satu
        $uniquePOsInfo = [];
        foreach(($items ?? []) as $itx){
            $p = $itx->no_po ?? ($itx->po->no_po ?? null);
            if($p){ $uniquePOsInfo[$p] = true; }
        }
        $multiPOInfo = count($uniquePOsInfo) > 1;

        // Tentukan No Invoice terbaru dari items
        $latestInvoiceNo = $invoiceDetails['invoice_no'] ?? '-';
        $bestNum = -1; $bestTime = null; $bestStr = null;
        foreach(($items ?? []) as $invIt){
            $invStr = $invIt->invoice_no ?? ($invIt->no_invoice ?? ($invIt->po->no_invoice ?? null));
            if ($invStr) {
                $parts = explode('/', (string)$invStr);
                $num = intval($parts[0] ?? -1);
            } else { $num = -1; }
            $tgl = $invIt->tanggal_po ?? ($invIt->po->tanggal_po ?? null);
            $ts = $tgl ? @strtotime($tgl) : null;
            if ($num >= 0) {
                if ($num > $bestNum) { $bestNum = $num; $bestStr = $invStr; $bestTime = $ts; }
                elseif ($num === $bestNum && $ts && (!$bestTime || $ts > $bestTime)) { $bestStr = $invStr; $bestTime = $ts; }
            } elseif ($ts) {
                if (!$bestTime || $ts > $bestTime) { $bestStr = $invStr; $bestTime = $ts; }
            }
        }
        if ($bestStr) { $latestInvoiceNo = $bestStr; }
    @endphp
    <table class="no-break" style="width:100%; border-collapse:collapse; margin:0;">
        <tr>
            <td style="width:33.33%; text-align:left; vertical-align:bottom; padding:0;">
                <span style="font-weight:bold;">No. PO : {{ $multiPOInfo ? '' : ($invoiceDetails['no_po'] ?? '-') }}</span>
            </td>
            <td style="width:33.33%; text-align:center; vertical-align:bottom; padding:0;">
                <span style="font-weight:bold;">No : {{ $latestInvoiceNo }}</span>
            </td>
            <td style="width:33.33%; text-align:right; vertical-align:bottom; padding:0;">
                <span style="font-weight:bold;">Date : {{ $invoiceDetails['invoice_date'] ?? '-' }}</span>
            </td>
        </tr>
    </table>

    @php
        // Deteksi apakah ada lebih dari satu PO pada daftar item
        $uniquePOs = [];
        foreach (($items ?? []) as $iit) {
            $poNoTmp = $iit->no_po ?? ($iit->po->no_po ?? null);
            if ($poNoTmp) { $uniquePOs[$poNoTmp] = true; }
        }
        $multiPO = count($uniquePOs) > 1;
    @endphp
    <table class="no-break" style="margin-top: 4px; margin-bottom: 0;">
        <thead>
            <tr>
                <th>DESCRIPTION</th>
                <th style="width:{{ $wQty }}">QTY</th>
                <th style="width:{{ $wUnit }}">UNIT PRICE</th>
                <th style="width:{{ $wAmt }}">AMMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $it)
                @php
                    $qty = (int)($it->qty ?? 0);
                    $total = (int)($it->total ?? 0);
                    $unit = $qty > 0 ? round($total / max(1,$qty)) : 0;
                    $jenis = ($it->qty_jenis ?? '') !== '' && ($it->qty_jenis ?? '0') !== '0' ? $it->qty_jenis : 'PCS';
                    $namaProduk = $it->produk->nama_produk ?? $it->produk->nama ?? $it->produk->name ?? '-';
                    $noPoItem = $it->no_po ?? ($it->po->no_po ?? ($invoiceDetails['no_po'] ?? null));
                    $namaProdukDenganNoPo = ($multiPO && $noPoItem) ? (trim($namaProduk).' ('.trim($noPoItem).')') : $namaProduk;
                @endphp
                <tr>
                    <td class="bold col-desc">{{ $namaProdukDenganNoPo }}</td>
                    <td class="bold" style="text-align:center;">{{ number_format($qty, 0, ',', '.') }} {{ $jenis }}</td>
                    <td class="bold" style="text-align:right;">Rp. {{ number_format($unit, 0, ',', '.') }}</td>
                    <td class="bold" style="text-align:right;">Rp. {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @php
                // Tambahkan baris kosong agar minimal 20 baris
                $minRows = 20;
                $fill = max(0, $minRows - $itemCount);
            @endphp
            @for($i = 0; $i < $fill; $i++)
                <tr>
                    <td class="col-desc">&nbsp;</td>
                    <td style="text-align:center;">&nbsp;</td>
                    <td style="text-align:right;">&nbsp;</td>
                    <td style="text-align:right;">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr>
                <td class="bold" style="text-align:right;">SUB TOTAL :</td>
                <td class="bold" style="text-align:center;">{{ number_format($invoiceDetails['total_qty'] ?? 0, 0, ',', '.') }}</td>
                <td></td>
                <td style="text-align:right;">Rp. {{ number_format($invoiceDetails['subtotal'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="bold" style="text-align:right;">PPN 11% :</td>
                <td></td>
                <td></td>
                <td style="text-align:right;">Rp. {{ number_format($invoiceDetails['ppn'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="bold" style="text-align:right;">GRAND TOTAL :</td>
                <td></td>
                <td></td>
                <td class="bold" style="text-align:right;">Rp. {{ number_format($invoiceDetails['grand_total'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row between no-break" style="margin-top: 2px; align-items:flex-start; gap: 6mm; flex-wrap: nowrap;">
        <div style="flex: 0 0 58%;">
            <p style="margin:0; font-size:{{ $tdRightFs }}px; line-height:1.25;">
                <strong>Pembayaran Mohon Di Transfer Ke rekening</strong><br>
                <strong>Bank BRI PEJATEN</strong><br>
                <strong>NO REK : 1182-01-000039-30-3</strong><br>
                <strong>ATAS NAMA : PT. CAM JAYA ABADI</strong>
            </p>
        </div>
        <div style="flex: 0 0 38%; margin-left:auto;">
            <!-- Kontainer 170px agar nama center tepat di bawah teks Bekasi -->
            <div style="width:170px; margin-left:auto; text-align:center;">
                <!-- Tanggal dekat ke tabel dan center -->
                <p style="margin: -55px 0 0 0; text-align:center; font-size:10px; font-family: DejaVu Sans, Arial, sans-serif;"><strong>Bekasi, {{ $invoiceDetails['date_location'] ?? ($invoiceDetails['invoice_date'] ?? '') }}</strong></p>
                <!-- Nama center di bawah tanggal dalam lebar yang sama (diturunkan lebih ke bawah) -->
                <div style="text-align:center; width:170px; margin:85px 0 0 0;">
                    <p style="margin:0; font-size:9.5px;">
                        <strong><u>NANIK PURWATI</u></strong><br>
                        <span style="font-size:8px;">DIREKTUR UTAMA</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
