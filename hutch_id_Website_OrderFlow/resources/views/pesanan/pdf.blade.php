<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $pesanan->nomor_po }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #20233b;
            margin: 0;
            padding: 24px;
            background: #f7f9fc;
        }

        .page {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(15, 64, 124, 0.08);
        }

        .header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e8edf5;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            letter-spacing: -0.02em;
        }

        .header p {
            margin: 4px 0 0;
            color: #5d6d85;
        }

        .section {
            margin-bottom: 22px;
        }

        .section strong {
            display: block;
            margin-bottom: 6px;
            color: #344767;
        }

        .section p {
            margin: 0;
            line-height: 1.6;
            color: #4e5874;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .table th,
        .table td {
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            text-align: left;
        }

        .table thead th {
            background: #f8fafc;
            color: #4f5d7a;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr:nth-child(even) {
            background: #fbfdff;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            margin-top: 18px;
            border-top: 1px solid #e8edf5;
            padding-top: 14px;
        }

        .summary p {
            margin: 0;
            color: #344767;
        }

        .small {
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h2>Purchase Order</h2>
            <p>No. {{ $pesanan->nomor_po }}</p>
            <p class="small">Tanggal Pesanan: {{ $pesanan->tanggal_pesanan->format('d M Y') }} | Tanggal Pengiriman: {{ $pesanan->tanggal_pengiriman->format('d M Y') }}</p>
        </div>

        <div class="section">
            <strong>Pelanggan</strong>
            <p>{{ $pesanan->pelanggan->nama }}<br>
            {{ $pesanan->pelanggan->alamat }}<br>
            {{ $pesanan->pelanggan->telepon }}<br>
            {{ $pesanan->pelanggan->email ?? '-' }}</p>
        </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Spesifikasi</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->detailPesanan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($item->produk)->nama ?? 'N/A' }}</td>
                    <td>{{ $item->spesifikasi ?? '-' }}</td>
                    <td class="text-right">{{ $item->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Nilai:</strong> Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ str_replace('_', ' ', $pesanan->status) }}</p>
    </div>

    <div class="summary small">
        <p>Dicetak oleh {{ $pesanan->creator->name ?? 'Sistem' }} pada {{ now()->format('d M Y H:i') }}</p>
    </div>
    </div>
</body>
</html>
