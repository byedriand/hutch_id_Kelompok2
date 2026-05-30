<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $pesanan->nomor_po }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #1a3a52;
            background: #f8fbff;
            padding: 0;
            line-height: 1.5;
        }

        .page {
            background: #ffffff;
            padding: 40px;
            margin: 0;
            min-height: 100vh;
            position: relative;
        }

        /* Header dengan Logo */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 3px solid #2d7dd2;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .brand-info h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #0f3d7f;
            letter-spacing: 0.05em;
        }

        .brand-info p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .po-info {
            text-align: right;
        }

        .po-info h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            color: #0f3d7f;
            letter-spacing: -0.02em;
        }

        .po-info p {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 12px;
        }

        /* Divider dekoratif */
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #2d7dd2, transparent);
            margin: 24px 0;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }

        .section {
            margin-bottom: 0;
        }

        .section-title {
            display: block;
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f3d7f;
        }

        .section-content {
            line-height: 1.7;
            color: #334155;
            font-size: 13px;
        }

        .section-content strong {
            color: #0f3d7f;
            font-weight: 600;
        }

        .section-content br {
            margin-bottom: 2px;
        }

        /* Tabel Modern */
        .table-section {
            margin: 32px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .table thead {
            background: linear-gradient(135deg, #0f3d7f 0%, #1d457c 100%);
        }

        .table th {
            color: #ffffff;
            padding: 14px 12px;
            text-align: left;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border: none;
        }

        .table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e8edf5;
            color: #334155;
        }

        .table tbody tr {
            background: #ffffff;
            transition: background-color 0.2s ease;
        }

        .table tbody tr:nth-child(even) {
            background: #f8fbff;
        }

        .table tbody tr:hover {
            background: #f0f4f8;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 2px solid #e8edf5;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 13px;
        }

        .summary-row.total {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.08), rgba(45, 125, 210, 0.04));
            padding: 14px;
            border-radius: 8px;
            margin-top: 12px;
            border-left: 4px solid #2d7dd2;
        }

        .summary-row.total strong {
            font-size: 16px;
            font-weight: 700;
            color: #0f3d7f;
        }

        .summary-row.total .amount {
            font-size: 16px;
            font-weight: 800;
            color: #2d7dd2;
        }

        .summary-row strong {
            color: #0f3d7f;
            font-weight: 600;
        }

        .summary-row .label {
            color: #6b7280;
        }

        .summary-row .amount {
            color: #1a3a52;
            font-weight: 600;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-selesai {
            background: rgba(34, 197, 94, 0.12);
            color: #166534;
        }

        .status-menunggu {
            background: rgba(234, 179, 8, 0.12);
            color: #92400e;
        }

        .status-dalam_produksi {
            background: rgba(59, 130, 246, 0.12);
            color: #1e40af;
        }

        .status-dikirim {
            background: rgba(99, 102, 241, 0.12);
            color: #312e81;
        }

        /* Footer */
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e8edf5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }

        .footer-info {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.6;
        }

        .footer-info strong {
            color: #1a3a52;
            font-weight: 600;
        }

        .signature-section {
            text-align: right;
            padding-top: 40px;
        }

        .signature-line {
            border-top: 1px solid #1a3a52;
            padding-top: 8px;
            margin-top: 40px;
            font-size: 11px;
            color: #1a3a52;
            font-weight: 600;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            opacity: 0.05;
            color: #0f3d7f;
            pointer-events: none;
            font-weight: 800;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">PURCHASE ORDER</div>
    
    <div class="page">
        <!-- Header dengan Logo -->
        <div class="top-header">
            <div class="logo-section">
                <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch Prestige Logo" class="logo-img">
                <div class="brand-info">
                    <h3>HUTCH PRESTIGE</h3>
                    <p>Bag Manufacturing & In-House Brand</p>
                </div>
            </div>
            <div class="po-info">
                <h2>PO</h2>
                <p>{{ $pesanan->nomor_po }}</p>
            </div>
        </div>

        <!-- Tanggal Info -->
        <div class="divider"></div>
        <div class="content-grid">
            <div class="section">
                <span class="section-title">Tanggal Pesanan</span>
                <div class="section-content">
                    <strong>{{ $pesanan->tanggal_pesanan->format('d F Y') }}</strong><br>
                    <span style="font-size: 12px; color: #6b7280;">{{ $pesanan->tanggal_pesanan->format('H:i') }} WIB</span>
                </div>
            </div>
            <div class="section">
                <span class="section-title">Target Pengiriman</span>
                <div class="section-content">
                    <strong>{{ $pesanan->tanggal_pengiriman->format('d F Y') }}</strong><br>
                    <span style="font-size: 12px; color: #6b7280;">{{ $pesanan->tanggal_pengiriman->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="section">
                <span class="section-title">Informasi Pelanggan</span>
                <div class="section-content">
                    <strong>{{ $pesanan->pelanggan->nama }}</strong><br>
                    {{ $pesanan->pelanggan->alamat }}<br>
                    <strong style="color: #6b7280;">{{ $pesanan->pelanggan->telepon }}</strong><br>
                    {{ $pesanan->pelanggan->email ?? '-' }}
                </div>
            </div>
            <div class="section">
                <span class="section-title">Status Pesanan</span>
                <div class="section-content">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '_', $pesanan->status)) }}">
                        {{ ucwords(str_replace('_', ' ', $pesanan->status)) }}
                    </span>
                    <p style="margin-top: 12px; color: #6b7280; font-size: 12px;">
                        Dibuat oleh: <strong>{{ $pesanan->creator->name ?? 'Sistem' }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabel Produk -->
        <div class="table-section">
            <h4 style="font-size: 13px; font-weight: 700; color: #0f3d7f; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Detail Pesanan</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>Produk</th>
                        <th>Spesifikasi</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 15%;">Harga Satuan</th>
                        <th style="width: 15%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->detailPesanan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><strong>{{ optional($item->produk)->nama ?? 'N/A' }}</strong></td>
                            <td>{{ $item->spesifikasi ?? '-' }}</td>
                            <td class="text-right"><strong>{{ $item->jumlah }}</strong></td>
                            <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-right"><strong>Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div style="max-width: 400px; margin-left: auto;">
                <div class="summary-row">
                    <span class="label">Subtotal</span>
                    <span class="amount">Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row total">
                    <strong>TOTAL NILAI</strong>
                    <span class="amount">Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <div class="footer-info">
                <strong>Catatan Penting:</strong><br>
                • Pembayaran sesuai syarat dan ketentuan<br>
                • Pesanan berlaku hingga tanggal pengiriman<br>
                • Hubungi kami untuk perubahan pesanan<br>
                <br>
                <strong>Kontak:</strong><br>
                Email: info@hutch.id<br>
                Website: www.hutch.id
            </div>
            <div class="signature-section">
                <p style="margin-bottom: 60px; color: #6b7280; font-size: 12px;">
                    Dicetak pada: {{ now()->format('d F Y H:i') }}
                </p>
                <div class="signature-line">
                    Staf Penjualan
                </div>
            </div>
        </div>
    </div>
</body>
</html>
