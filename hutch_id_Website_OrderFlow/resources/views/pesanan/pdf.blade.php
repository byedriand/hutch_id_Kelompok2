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
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background: white;
            padding: 0;
            font-size: 12px;
            color: #2c3e50;
        }

        .invoice {
            max-width: 100%;
            margin: 0;
            background: white;
            padding: 30px 45px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 22px;
            border-bottom: 1px solid #cccccc;
        }

        .logo {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .logo-box {
            width: 85px;
            height: 85px;
            border: 2px solid #333333;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: white;
            flex-shrink: 0;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 3px;
        }

        .logo-text h3 {
            font-size: 14px;
            font-weight: 900;
            color: #333333;
            margin: 0;
            letter-spacing: 0px;
            text-transform: uppercase;
        }

        .logo-text p {
            color: #666666;
            margin: 2px 0 0 0;
            font-size: 10px;
            font-weight: 400;
            letter-spacing: 0px;
            text-transform: capitalize;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            font-size: 48px;
            font-weight: 900;
            margin: 0;
            color: #333333;
            line-height: 1;
            letter-spacing: 0px;
        }

        .status {
            display: inline-block;
            padding: 6px 14px;
            background: #e8e8e8;
            border-radius: 4px;
            font-weight: 700;
            color: #666666;
            font-size: 10px;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0px;
            border-left: none;
        }

        hr {
            display: none;
        }

        /* Info Section */
        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 100px;
            margin-bottom: 25px;
            padding: 20px 0;
            background: transparent;
            border-left: none;
            border-radius: 0px;
        }

        .info h4 {
            color: #666666;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .info p {
            margin-bottom: 6px;
            color: #333333;
            font-size: 12px;
            line-height: 1.8;
        }

        .company-name {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 12px;
            color: #333333;
            letter-spacing: 0px;
        }

        .info strong {
            color: #333333;
            font-weight: 700;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0 30px;
            background: white;
            box-shadow: none;
            border-radius: 0px;
            overflow: visible;
            border-top: 1px solid #cccccc;
            border-bottom: 1px solid #cccccc;
        }

        .table th {
            background: #f0f0f0;
            padding: 12px 15px;
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #666666;
            border: none;
            box-shadow: none;
        }

        .table td {
            padding: 14px 15px;
            border-bottom: 1px solid #e8e8e8;
            font-size: 12px;
            color: #333333;
            font-weight: 400;
            vertical-align: middle;
        }

        .table td:first-child {
            padding-left: 15px;
        }

        .table td.text-right {
            padding-right: 15px;
        }

        .table tbody tr:nth-child(odd) {
            background: white;
        }

        .table tbody tr:hover {
            background: white;
        }

        .table tbody tr:last-child td {
            border-bottom: 1px solid #cccccc;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* Summary Section */
        .summary-box {
            margin-left: auto;
            width: 340px;
            background: #f5f5f5;
            padding: 0;
            border-radius: 0px;
            border: 1px solid #cccccc;
            overflow: hidden;
            box-shadow: none;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 18px;
            font-size: 11px;
            color: #333333;
            border-bottom: 1px solid #e8e8e8;
            background: transparent;
        }

        .summary-row:last-child {
            border-bottom: 1px solid #e8e8e8;
        }

        .summary-row .label {
            flex: 1;
            font-weight: 600;
            color: #666666;
            text-transform: uppercase;
            font-size: 10px;
        }

        .summary-row .amount {
            text-align: right;
            min-width: 120px;
            font-weight: 700;
            color: #333333;
        }

        .summary-row.total {
            background: #f5f5f5;
            padding: 14px 18px;
            font-size: 12px;
            font-weight: 900;
            color: #333333;
            text-transform: uppercase;
            letter-spacing: 0px;
            box-shadow: none;
            border-bottom: none;
            border-top: 1px dashed #cccccc;
        }

        .summary-row.total .label {
            color: #333333;
            font-size: 11px;
        }

        .summary-row.total .amount {
            font-size: 13px;
            color: #333333;
        }
    </style>
</head>
<body>

    <div class="invoice">

        <!-- Header -->
        <div class="header">
            <div class="logo">
                <div class="logo-box">
                    <img src="{{ public_path('images/hutch-logo.png') }}" alt="Hutch Prestige Logo">
                </div>
                <div class="logo-text">
                    <h3>Hutch Prestige</h3>
                    <p>Bag Manufacturing</p>
                </div>
            </div>

            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="status">{{ $pesanan->status ? ucwords(str_replace('_', ' ', $pesanan->status)) : 'PENDING' }}</div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info">
            <div>
                <h4>Kepada</h4>
                <div class="company-name">{{ $pesanan->pelanggan->nama ?? 'Pelanggan' }}</div>
                <p>👤 {{ $pesanan->pelanggan->pic ?? 'Kontak Utama' }}</p>
                <p>📞 {{ $pesanan->pelanggan->telepon ?? '+62 XXX-XXXX-XXXX' }}</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <div>
                    <h4>No Invoice</h4>
                    <p style="font-weight: 700; font-size: 13px;">{{ $pesanan->nomor_po ?? 'PO-XXXXXXX' }}</p>
                </div>
                <div>
                    <h4>Tanggal</h4>
                    <p style="font-weight: 400;">
                        @php
                            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            if($pesanan->tanggal_pesanan) {
                                $date = $pesanan->tanggal_pesanan;
                                $dayName = $days[$date->dayOfWeek];
                                $monthName = $months[$date->month - 1];
                                echo $dayName . ', ' . $date->day . ' ' . $monthName . ' ' . $date->year;
                            } else {
                                echo 'Tanggal';
                            }
                        @endphp
                    </p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 45%; text-align: left;">Keterangan</th>
                    <th style="width: 20%; text-align: right;">Harga</th>
                    <th style="width: 15%; text-align: center;">Jumlah</th>
                    <th style="width: 20%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan->detailPesanan as $item)
                    <tr>
                        <td style="width: 45%; text-align: left;">{{ optional($item->produk)->nama ?? 'Produk' }}</td>
                        <td style="width: 20%; text-align: right;">Rp{{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                        <td style="width: 15%; text-align: center;">{{ $item->jumlah ?? 0 }}</td>
                        <td style="width: 20%; text-align: right;">Rp{{ number_format(($item->jumlah ?? 0) * ($item->harga_satuan ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #999999; font-style: italic;">Belum ada item produk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary Box -->
        <div class="summary-box">
            <div class="summary-row">
                <span class="label">Subtotal</span>
                <span class="amount">Rp{{ number_format($pesanan->total_nilai ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Pajak</span>
                <span class="amount">Rp0</span>
            </div>
            <div class="summary-row total">
                <span class="label">Total</span>
                <span class="amount">Rp{{ number_format($pesanan->total_nilai ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>
</body>
</html>
