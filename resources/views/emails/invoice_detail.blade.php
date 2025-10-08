<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - PT. CAM JAYA ABADI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
            color: #2c3e50;
            background: #ecf0f1;
            padding: 20px 10px;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #ffffff;
            padding: 28px 24px 20px 24px;
            text-align: center;
            border-bottom: 1px solid #dfe3e6;
        }
        .logo {
            margin: 0 auto 12px;
        }
        .logo img {
            display: block;
            margin: 0 auto;
            max-width: 180px; /* bebas, mengikuti rasio asli */
            max-height: 70px;
            height: auto;
            width: auto;
            object-fit: contain;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 2px;
            color: #2c3e50;
            letter-spacing: .5px;
        }
        .header p {
            font-size: 13px;
            color: #7f8c8d;
            font-weight: 500;
        }
        .content {
            padding: 22px 24px 26px 24px;
        }
        .greeting {
            font-size: 14px;
            margin-bottom: 16px;
            color: #2c3e50;
            line-height: 1.6;
        }
        .greeting strong {
            color: #34495e;
            font-size: 17px;
        }
        .info-box {
            background: #f8f9fa;
            border: 1px solid #e6e9ec;
            padding: 16px 16px 8px 16px;
            margin: 14px 0 18px 0;
            border-radius: 6px;
        }
        .info-row {
            display: table; /* lebih stabil di email client untuk alignment */
            width: 100%;
            border-collapse: collapse;
            padding: 0; /* padding dipindah ke cell */
            border-bottom: 1px dashed #e1e5e8;
        }
        .info-row:last-child {
            border-bottom: none;
            padding-top: 10px;
            margin-top: 6px;
        }
        .label {
            display: table-cell;
            width: 56%; /* kolom kiri fix width agar nilai rata kanan */
            padding: 8px 12px 8px 0;
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
            vertical-align: baseline;
            white-space: nowrap;
        }
        .value {
            display: table-cell;
            width: 44%;
            padding: 8px 0 8px 12px;
            color: #1f2937;
            font-size: 13px;
            font-weight: 600;
            text-align: right;
            vertical-align: baseline;
            white-space: nowrap;
        }
        .value.total {
            font-size: 16px;
            font-weight: 700;
            color: #34495e;
        }
        .section-title {
            color: #111827;
            font-size: 15px;
            font-weight: 700;
            margin: 16px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e6e9ec;
        }
        .item-list { margin: 10px 0 6px 0; padding-left: 0; }
        .item { list-style: none; padding: 8px 0; display: table; width: 100%; border-bottom: 1px dashed #e6e9ec; }
        .item:last-child { border-bottom: none; }
        .item-name { display: table-cell; width: 70%; padding-right: 12px; font-weight: 600; color: #111827; font-size: 14px; vertical-align: baseline; }
        .item-qty { display: table-cell; width: 30%; text-align: right; vertical-align: baseline; color: #374151; font-weight: 600; font-size: 12px; background: #eef2f7; padding: 4px 10px; border-radius: 999px; white-space: nowrap; }
        .payment-box { background: #f8f9fa; border: 1px solid #e6e9ec; padding: 14px; margin: 16px 0; border-radius: 6px; }
        .payment-box h4 { font-size: 14px; margin-bottom: 8px; font-weight: 700; color: #111827; }
        .payment-box p { margin: 5px 0; font-size: 13px; color: #374151; line-height: 1.5; }
        .payment-box strong { color: #111827; }
        .note {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin: 25px 0;
            border-left: 4px solid #34495e;
        }
        .note p {
            margin: 8px 0;
            color: #2c3e50;
            line-height: 1.7;
        }
        .footer { background: #f9fafb; text-align: center; padding: 14px 16px; color: #6b7280; font-size: 12px; border-top: 1px solid #e6e9ec; }
        .footer p { margin: 4px 0; line-height: 1.5; }
        .footer strong { color: #111827; }
        
        /* Mobile Responsive */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px 5px;
            }
            .header { padding: 18px 16px 14px 16px; }
            .logo img { max-width: 150px; max-height: 56px; }
            .header h1 { font-size: 20px; }
            .content { padding: 16px; }
            .info-row {
                flex-direction: column;
            }
            .value {
                text-align: left;
                margin-top: 5px;
            }
            .item { padding: 8px 0; }
            .item-name { width: 68%; }
            .item-qty { width: 32%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                @php
                    $logoBase64 = '';
                    $candidates = [
                        'image/cam.png',
                        'image/cam.jpg',
                        'image/cam.jpeg',
                        'image/cam.webp',
                    ];
                    foreach ($candidates as $relPath) {
                        $abs = public_path($relPath);
                        if (file_exists($abs)) {
                            $mime = function_exists('mime_content_type') ? mime_content_type($abs) : 'image/png';
                            $data = @file_get_contents($abs);
                            if ($data !== false) {
                                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
                                break;
                            }
                        }
                    }
                @endphp
                <!-- Prefer CID (embedded via Mailable). Fallback to base64 if available. -->
                <img src="cid:logo" alt="PT. CAM JAYA ABADI" style="display:block; max-width:180px; max-height:70px; height:auto; width:auto; object-fit:contain;"
                     onerror="this.onerror=null; this.src='{{ $logoBase64 ?: '' }}'; if(!this.src){ this.outerHTML='\x3cdiv style=\'font-size:14px;color:#7f8c8d;font-weight:600;\'\x3ePT. CAM\x3cbr\x3eJAYA\x3cbr\x3eABADI\x3c/div\x3e'; }">
            </div>
            <h1>TAGIHAN JATUH TEMPO</h1>
            <p>PT. CAM JAYA ABADI</p>
        </div>

        <div class="content">
            <p class="greeting">Kepada Yth,<br><strong>{{ $jatuhTempo->customer }}</strong></p>

            <div class="info-box">
                <div class="info-row">
                    <span class="label">No. Invoice</span>
                    <span class="value">{{ $jatuhTempo->no_invoice }}</span>
                </div>
                <div class="info-row">
                    <span class="label">No. PO</span>
                    <span class="value">{{ $jatuhTempo->no_po }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal Invoice</span>
                    <span class="value">{{ \Carbon\Carbon::parse($jatuhTempo->tanggal_invoice)->format('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal Jatuh Tempo</span>
                    <span class="value">{{ \Carbon\Carbon::parse($jatuhTempo->tanggal_jatuh_tempo)->format('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Tagihan</span>
                    <span class="value total">Rp {{ number_format($jatuhTempo->jumlah_tagihan ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <h3 class="section-title">Daftar Barang yang Dipesan</h3>
        
            @if($items && $items->count() > 0)
            <div class="item-list">
                @foreach($items as $item)
                    @php
                        $qty = (int)($item->qty ?? 0);
                        $jenis = strtoupper($item->qty_jenis ?? 'PCS');
                        $namaBarang = $item->produkRel->nama_produk ?? $item->barang ?? 'Barang tidak diketahui';
                    @endphp
                    <div class="item">
                        <span class="item-name">{{ $namaBarang }}</span>
                        <span class="item-qty">{{ number_format($qty, 0, ',', '.') }} {{ $jenis }}</span>
                    </div>
                @endforeach
            </div>
            @else
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px; color: #856404;">
                <strong>⚠️ Perhatian:</strong> Data barang tidak tersedia untuk invoice ini.
            </div>
            @endif

            <div class="payment-box">
                <h4>💳 Informasi Pembayaran</h4>
                <p><strong>Bank:</strong> BRI PEJATEN</p>
                <p><strong>No. Rekening:</strong> 1182-01-000039-30-3</p>
                <p><strong>Atas Nama:</strong> PT. CAM JAYA ABADI</p>
            </div>

            <div class="note">
                <p style="margin-bottom: 10px;">
                    <strong>Terima kasih</strong> atas kepercayaan Anda kepada PT. CAM JAYA ABADI.
                </p>
                <p style="color: #7f8c8d; font-size: 14px;">
                    Jika ada pertanyaan mengenai invoice ini, silakan hubungi kami di telepon <strong style="color: #2c3e50;">(021) 6617 1626</strong> atau balas email ini.
                </p>
            </div>
        </div>

    <div class="footer">
        <p>Email ini dikirim oleh <strong>{{ $adminName }}</strong> ({{ $adminEmail }})</p>
        <p><strong>PT. CAM JAYA ABADI</strong></p>
        <p>Jl. Wahana Bakti No.28, Mangunjaya, Kec. Tambun Sel. Bekasi Jawa Barat 17510</p>
        <p>Telp: (021) 6617 1626 - Fax: (021) 6617 3986</p>
    </div>
    </div>
</body>
</html>
