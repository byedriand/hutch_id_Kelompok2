<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>hutch.id — Dashboard Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ===================== VARIABLES ===================== */
:root {
  --navy:    #0f2744;
  --blue:    #1a3f6f;
  --accent:  #2d7dd2;
  --light:   #e8f0fa;
  --green:   #16a34a;
  --red:     #dc2626;
  --yellow:  #d97706;
  --gray:    #64748b;
  --bg:      #f0f4fa;
  --white:   #ffffff;
  --border:  #d1dce8;
  --shadow:  0 2px 16px rgba(15,39,68,0.10);
  --radius:  10px;
}

/* ===================== RESET ===================== */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--navy); font-size: 13px; }
a { text-decoration: none; color: inherit; }
input, select, textarea, button { font-family: inherit; }

/* ===================== LAYOUT ===================== */
.app { display: flex; height: 100vh; overflow: hidden; }

/* ===================== SIDEBAR ===================== */
.sidebar {
  width: 220px; background: var(--navy); display: flex;
  flex-direction: column; flex-shrink: 0; overflow-y: auto;
  transition: width .25s;
}
.sidebar-brand { padding: 20px 18px 16px; border-bottom: 1px solid rgba(255,255,255,.08); }
.brand-logo { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -1px; }
.brand-sub  { font-size: 10px; color: rgba(255,255,255,.4); margin-top: 2px; line-height: 1.4; }
.sidebar-section {
  padding: 14px 16px 4px; font-size: 9.5px; font-weight: 700;
  color: rgba(255,255,255,.28); letter-spacing: 1.2px; text-transform: uppercase;
}
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 14px; margin: 1px 8px; border-radius: 8px;
  color: rgba(255,255,255,.55); cursor: pointer; font-size: 12.5px; font-weight: 500;
  transition: background .15s, color .15s;
}
.nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
.nav-item.active { background: var(--accent); color: #fff; }
.nav-icon { font-size: 15px; width: 20px; text-align: center; flex-shrink: 0; }
.nav-badge {
  margin-left: auto; background: #ef4444; color: #fff;
  font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 10px;
}
.sidebar-bottom {
  margin-top: auto; padding: 14px; border-top: 1px solid rgba(255,255,255,.08);
}
.user-card { display: flex; align-items: center; gap: 10px; }
.user-av {
  width: 32px; height: 32px; border-radius: 50%; background: var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
}
.user-name { font-size: 12px; font-weight: 600; color: #fff; }
.user-role { font-size: 10px; color: rgba(255,255,255,.4); margin-top: 1px; }

/* ===================== MAIN ===================== */
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.topbar {
  background: var(--white); border-bottom: 1px solid var(--border);
  padding: 13px 22px; display: flex; justify-content: space-between;
  align-items: center; flex-shrink: 0; gap: 12px;
}
.topbar-left { display: flex; align-items: center; gap: 10px; }
.topbar-title { font-size: 16px; font-weight: 700; }
.topbar-subtitle { font-size: 11px; color: var(--gray); margin-top: 1px; }
.topbar-right { display: flex; gap: 8px; align-items: center; }
.content { flex: 1; overflow-y: auto; padding: 20px 22px; }

/* ===================== BUTTONS ===================== */
.btn {
  padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600;
  border: none; cursor: pointer; display: inline-flex; align-items: center;
  gap: 6px; transition: all .15s; white-space: nowrap;
}
.btn-primary   { background: var(--accent); color: #fff; }
.btn-primary:hover { background: #1a6cbd; }
.btn-secondary { background: var(--white); color: var(--navy); border: 1px solid var(--border); }
.btn-secondary:hover { background: #f8fafc; }
.btn-success   { background: #dcfce7; color: var(--green); }
.btn-success:hover { background: #bbf7d0; }
.btn-danger    { background: #fee2e2; color: var(--red); }
.btn-danger:hover { background: #fecaca; }
.btn-sm { padding: 5px 11px; font-size: 11.5px; }

/* ===================== CARDS ===================== */
.card { background: var(--white); border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 16px; overflow: hidden; }
.card-header { padding: 13px 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.card-title { font-size: 13px; font-weight: 700; }
.card-body { padding: 16px; }

/* ===================== STAT CARDS ===================== */
.stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 18px; }
.stat-card { background: var(--white); border-radius: var(--radius); padding: 16px 18px; border: 1px solid var(--border); }
.stat-label { font-size: 11px; color: var(--gray); font-weight: 500; display: flex; align-items: center; gap: 6px; }
.stat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.stat-value { font-size: 28px; font-weight: 800; line-height: 1.1; margin: 5px 0 3px; }
.stat-desc { font-size: 10.5px; color: var(--gray); }

/* ===================== TABLE ===================== */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; min-width: 600px; }
th {
  font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase;
  letter-spacing: .5px; padding: 9px 13px; background: #f8fafc;
  border-bottom: 1px solid var(--border); text-align: left; white-space: nowrap;
}
td { padding: 10px 13px; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; vertical-align: middle; }
tr:last-child td { border-bottom: none; }
tr:hover td { background: #fafcff; }

/* ===================== BADGES ===================== */
.badge {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600;
}
.b-wait   { background: #fef3c7; color: #92400e; }
.b-conf   { background: #dbeafe; color: #1e40af; }
.b-prod   { background: #f3e8ff; color: #6b21a8; }
.b-ready  { background: #dcfce7; color: #166534; }
.b-done   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.b-cancel { background: #f1f5f9; color: #64748b; }
.b-ok     { background: #dcfce7; color: var(--green); }
.b-warn   { background: #fee2e2; color: var(--red); }

.mono { font-family: 'Fira Code', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; }

/* ===================== FORMS ===================== */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: 1/-1; }
label { font-size: 11.5px; font-weight: 600; color: var(--navy); }
.input-hint { font-size: 10px; color: var(--gray); }
input, select, textarea {
  padding: 9px 12px; border: 1.5px solid var(--border); border-radius: 8px;
  font-size: 12.5px; color: var(--navy); background: #fff; outline: none;
  transition: border-color .15s;
}
input:focus, select:focus, textarea:focus { border-color: var(--accent); }
input[readonly] { background: #f8fafc; color: var(--gray); cursor: default; }
textarea { resize: vertical; min-height: 64px; }
.input-group { position: relative; }
.input-group input { padding-right: 36px; }
.input-group-icon { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: var(--gray); font-size: 14px; }

/* ===================== ITEMS TABLE ===================== */
.items-table { width: 100%; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; margin-bottom: 8px; }
.items-table th { font-size: 10.5px; }
.items-table td { padding: 8px 11px; }
.items-table input, .items-table select { padding: 6px 8px; font-size: 12px; border-radius: 6px; }

/* ===================== DETAIL ===================== */
.detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 18px; }
.info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid #f1f5f9; gap: 12px; }
.info-row:last-child { border-bottom: none; }
.info-label { font-size: 11.5px; color: var(--gray); flex-shrink: 0; }
.info-value { font-size: 12.5px; font-weight: 600; text-align: right; }

/* ===================== TIMELINE ===================== */
.timeline { list-style: none; padding-left: 22px; position: relative; }
.timeline::before { content:''; position: absolute; left: 7px; top: 0; bottom: 0; width: 2px; background: var(--border); }
.tl-item { position: relative; padding: 0 0 16px 16px; }
.tl-item::before {
  content:''; position: absolute; left: -15px; top: 3px;
  width: 10px; height: 10px; border-radius: 50%;
  background: var(--accent); border: 2px solid #fff; box-shadow: 0 0 0 1.5px var(--accent);
}
.tl-item.done::before { background: var(--green); box-shadow: 0 0 0 1.5px var(--green); }
.tl-status { font-size: 12px; font-weight: 700; }
.tl-meta { font-size: 10.5px; color: var(--gray); margin-top: 2px; }

/* ===================== FILTER ===================== */
.filter-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
.filter-row input, .filter-row select { padding: 7px 11px; font-size: 12px; }

/* ===================== ALERT BANNERS ===================== */
.alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 600; color: var(--green); margin-bottom: 14px; }
.alert-warn    { background: #fef2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; font-size: 12.5px; font-weight: 600; color: var(--red); margin-bottom: 14px; }

/* ===================== CUSTOMER GRID ===================== */
.cust-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.cust-card { background: #f8fafc; border: 1px solid var(--border); border-radius: 9px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: flex-start; }
.cust-name   { font-size: 13.5px; font-weight: 700; }
.cust-detail { font-size: 11px; color: var(--gray); margin-top: 4px; line-height: 1.7; }
.cust-actions { display: flex; flex-direction: column; gap: 5px; }

/* ===================== PDF PREVIEW ===================== */
.pdf-wrap { max-width: 980px; margin: 0 auto; background: #fff; border: 2px solid var(--border); border-radius: 10px; padding: 28px; }
.pdf-logo { font-size: 24px; font-weight: 800; color: var(--navy); letter-spacing: -1px; }
.pdf-tag  { font-size: 10px; color: var(--gray); line-height: 1.6; margin-top: 2px; }
.pdf-divider { height: 2.5px; background: var(--navy); margin: 12px 0; }
.pdf-section-title { font-size: 9.5px; font-weight: 700; text-transform: uppercase; color: var(--gray); letter-spacing: .6px; margin: 12px 0 5px; }
.pdf-table { width: 100%; font-size: 11px; border-collapse: collapse; }
.pdf-table th { background: var(--navy); color: #fff; padding: 6px 9px; font-size: 10px; text-align: left; }
.pdf-table td { padding: 6px 9px; border-bottom: 1px solid #e8ecf0; }
.pdf-total { display: flex; justify-content: flex-end; margin-top: 10px; }
.pdf-total-box { background: var(--navy); color: #fff; padding: 9px 16px; border-radius: 7px; font-size: 13px; font-weight: 700; }
.sign-row { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; margin-top: 22px; }
.sign-box { border: 1px solid var(--border); border-radius: 7px; padding: 12px; text-align: center; }
.sign-line { height: 44px; border-bottom: 1px dashed var(--border); margin-bottom: 8px; }
.sign-label { font-size: 10px; color: var(--gray); }
.pdf-footer { text-align: center; font-size: 9px; color: var(--gray); margin-top: 14px; padding-top: 10px; border-top: 1px solid var(--border); }

/* ===================== PAGE VISIBILITY ===================== */
.page { display: none; }
.page.active { display: block; }

/* ===================== RESPONSIVE ===================== */
@media (max-width: 900px) {
  .stat-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 640px) {
  .sidebar { width: 56px; }
  .brand-sub, .nav-item span:not(.nav-icon), .user-name, .user-role, .sidebar-section { display: none; }
  .nav-item { justify-content: center; padding: 10px; }
  .stat-grid { grid-template-columns: 1fr; }
}

/* ===================== ANIMATIONS ===================== */
@keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
.card, .stat-card { animation: fadeUp .25s ease both; }
.stat-grid .stat-card:nth-child(1) { animation-delay: .04s; }
.stat-grid .stat-card:nth-child(2) { animation-delay: .08s; }
.stat-grid .stat-card:nth-child(3) { animation-delay: .12s; }
.stat-grid .stat-card:nth-child(4) { animation-delay: .16s; }
</style>
</head>
<body>

<div class="app">

  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-logo">hutch.id</div>
      <div class="brand-sub">Modul Manajemen Pesanan</div>
    </div>
    <div class="sidebar-section">Menu Utama</div>
    <div class="nav-item active" data-page="dashboard" onclick="navigate(this)">
      <span class="nav-icon">📊</span><span>Dashboard</span>
    </div>
    <div class="nav-item" data-page="list" onclick="navigate(this)">
      <span class="nav-icon">📋</span><span>Daftar Pesanan</span>
      <span class="nav-badge">{{ $stats['menunggu_konfirmasi'] }}</span>
    </div>
    <div class="nav-item" data-page="create" onclick="navigate(this)">
      <span class="nav-icon">➕</span><span>Buat PO Baru</span>
    </div>
    <div class="nav-item" data-page="customer" onclick="navigate(this)">
      <span class="nav-icon">👥</span><span>Pelanggan</span>
    </div>
    <div class="sidebar-section">Lainnya</div>
    <div class="nav-item" onclick="navigate(this)" data-page="pdf">
      <span class="nav-icon">📄</span><span>Arsip PDF</span>
    </div>
    <div class="sidebar-bottom">
      <div class="user-card">
        <div class="user-av">AD</div>
        <div>
          <div class="user-name">Admin</div>
          <div class="user-role">Administrator</div>
        </div>
      </div>
      <button class="btn btn-secondary btn-sm" style="width:100%;margin-top:10px;justify-content:center">
        Keluar
      </button>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="main">

    <!-- ===== DASHBOARD ===== -->
    <div id="page-dashboard" class="page active">
      <div class="topbar">
        <div>
          <div class="topbar-title">Dashboard Pesanan</div>
          <div class="topbar-subtitle" id="dash-date">{{ now()->format('l, j M Y · H:i') }} WIB</div>
        </div>
        <button class="btn btn-primary" onclick="navigate(document.querySelector('[data-page=create]'))">
          ＋ Buat PO Baru
        </button>
      </div>
      <div class="content">
        <div class="stat-grid">
          <div class="stat-card">
            <div class="stat-label"><span class="stat-dot" style="background:var(--accent)"></span>Total PO Aktif</div>
            <div class="stat-value">{{ $stats['total_po'] }}</div>
            <div class="stat-desc">Bulan {{ now()->format('F Y') }}</div>
          </div>
        <div class="stat-card">
          <div class="stat-label"><span class="stat-dot" style="background:var(--yellow)"></span>Menunggu Konfirmasi</div>
          <div class="stat-value" style="color:var(--yellow)">{{ $stats['menunggu_konfirmasi'] }}</div>
          <div class="stat-desc">Perlu tindakan segera</div>
        </div>
        <div class="stat-card">
          <div class="stat-label"><span class="stat-dot" style="background:var(--green)"></span>Siap Kirim</div>
          <div class="stat-value" style="color:var(--green)">{{ $stats['siap_kirim'] }}</div>
          <div class="stat-desc">Menunggu pengiriman</div>
        </div>
        <div class="stat-card">
          <div class="stat-label"><span class="stat-dot" style="background:#94a3b8"></span>Selesai Bulan Ini</div>
          <div class="stat-value">{{ $stats['selesai_bulan'] }}</div>
          <div class="stat-desc">Nilai Rp {{ number_format($stats['nilai_selesai'], 0, ',', '.') }}</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">⚠ Menunggu Konfirmasi ({{ $stats['menunggu_konfirmasi'] }})</div>
          <button class="btn btn-secondary btn-sm">Lihat Semua</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Nomor PO</th><th>Pelanggan</th><th>Produk</th><th>Nilai PO</th><th>Tgl Kirim</th><th>Stok</th><th>Aksi</th></tr></thead>
            <tbody>
              @foreach($po_menunggu as $po)
              <tr>
                <td><span class="mono">{{ $po['nomor'] }}</span></td>
                <td>{{ $po['pelanggan'] }}</td>
                <td>{{ $po['produk'] }}</td>
                <td>Rp {{ number_format($po['nilai'], 0, ',', '.') }}</td>
                <td>{{ $po['tgl_kirim'] }}</td>
                <td><span class="badge {{ $po['stok'] == 'tersedia' ? 'b-ok' : 'b-warn' }}">{{ $po['stok'] == 'tersedia' ? '✓ Tersedia' : '⚠ Kurang' }}</span></td>
                <td><button class="btn btn-success btn-sm">Konfirmasi</button></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">📦 Dalam Produksi ({{ count($po_produksi) }})</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Nomor PO</th><th>Pelanggan</th><th>Produk</th><th>Nilai PO</th><th>Target Selesai</th><th>Status</th></tr></thead>
            <tbody>
              @foreach($po_produksi as $po)
              <tr>
                <td><span class="mono" style="color:var(--gray)">{{ $po['nomor'] }}</span></td>
                <td>{{ $po['pelanggan'] }}</td>
                <td>{{ $po['produk'] }}</td>
                <td>Rp {{ number_format($po['nilai'], 0, ',', '.') }}</td>
                <td>{{ $po['target_selesai'] }}</td>
                <td><span class="badge b-prod">🔄 Dalam Produksi</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

    <!-- ===== BUAT PO ===== -->
    <div id="page-create" class="page">
      <div class="topbar">
        <div>
          <div class="topbar-title">Buat Pesanan Baru</div>
          <div class="topbar-subtitle">REQ-PO-001 s/d REQ-PO-007</div>
        </div>
        <div class="topbar-right">
          <button class="btn btn-secondary" onclick="navigate(document.querySelector('[data-page=list]'))">Batal</button>
          <button class="btn btn-primary" onclick="savePO()">💾 Simpan Pesanan</button>
        </div>
      </div>
      <div class="content">
        <div class="card">
          <div class="card-header"><div class="card-title">📋 Informasi PO</div></div>
          <div class="card-body">
            <div class="form-grid">
              <div class="form-group">
                <label>Nomor PO (Auto-generate)</label>
                <input value="PO-{{ date('Ymd') }}-004" readonly class="mono" style="font-size:13px">
                <div class="input-hint">Format: PO-YYYYMMDD-XXX · tidak dapat diubah setelah disimpan (REQ-PO-002)</div>
              </div>
              <div class="form-group">
                <label>Tanggal Pesanan</label>
                <input type="date" value="{{ date('Y-m-d') }}" readonly>
              </div>
              <div class="form-group">
                <label>Pelanggan *</label>
                <input id="cust-input" placeholder="Ketik nama atau nomor telepon..." value="Budi Bag Store" oninput="autocomplete(this.value)">
                <div class="input-hint">Autocomplete dari master pelanggan (REQ-PO-003)</div>
              </div>
              <div class="form-group">
                <label>Tanggal Pengiriman Diminta *</label>
                <input type="date" id="tgl-kirim" value="2026-04-28">
                <div class="input-hint" id="tgl-hint" style="color:var(--green)">✓ Tanggal valid (REQ-PO-006)</div>
              </div>
              <div class="form-group full">
                <label>Alamat Pelanggan (auto-isi)</label>
                <input value="Jl. Sudirman No. 45, Jakarta Selatan, 12190">
              </div>
              <div class="form-group">
                <label>Telepon Pelanggan (auto-isi)</label>
                <input value="0812-3456-7890" readonly>
              </div>
              <div class="form-group">
                <label>Email Pelanggan (auto-isi)</label>
                <input value="budi@bagstore.id" readonly>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">🛍 Item Pesanan</div>
            <button class="btn btn-secondary btn-sm" onclick="addItem()">＋ Tambah Item</button>
          </div>
          <div class="card-body">
            <div class="table-wrap">
              <table class="items-table">
                <thead><tr><th>#</th><th>Nama Produk</th><th>Spesifikasi / Catatan</th><th>Qty</th><th>Harga Satuan (Rp)</th><th>Subtotal (Rp)</th><th></th></tr></thead>
                <tbody id="items-tbody">
                  <tr id="item-1">
                    <td>1</td>
                    <td><select style="width:100%;min-width:140px"><option>Tas Kanvas Custom</option><option>Tas Punggung</option><option>Tas Selempang</option><option>Dompet Kulit</option></select></td>
                    <td><input placeholder="Warna, ukuran, desain..." value="Hitam, 40×30cm" style="width:100%;min-width:120px"></td>
                    <td><input type="number" value="50" min="1" style="width:65px" oninput="calcRow(1)"></td>
                    <td><input value="150.000" style="width:95px" readonly class="mono"></td>
                    <td style="font-weight:700;color:var(--accent)" id="sub-1" class="mono">7.500.000</td>
                    <td><button style="background:none;border:none;color:var(--red);cursor:pointer;font-size:16px;padding:0 4px" onclick="removeItem(1)">✕</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-top:12px;padding-top:12px;border-top:1px solid var(--border);gap:16px;align-items:center">
              <div style="font-size:12px;color:var(--gray)">REQ-PO-005 · Dihitung otomatis real-time</div>
              <div style="text-align:right">
                <div style="font-size:11px;color:var(--gray)">Total Nilai PO</div>
                <div style="font-size:22px;font-weight:800" id="total-po">Rp 7.500.000</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title">📝 Catatan Khusus (Opsional)</div></div>
          <div class="card-body">
            <textarea placeholder="Instruksi produksi, permintaan desain, catatan untuk pelanggan...">Logo bordir di bagian depan, warna benang putih</textarea>
            <div class="input-hint" style="margin-top:6px">REQ-PO-007 · Field opsional untuk instruksi spesifik pelanggan</div>
          </div>
        </div>
      </div>
    </div>
    <div id="page-list" class="page">
      <div class="topbar">
        <div>
          <div class="topbar-title">Daftar Pesanan</div>
          <div class="topbar-subtitle">Semua Purchase Order · REQ-PO-023</div>
        </div>
        <button class="btn btn-primary" onclick="navigate(document.querySelector('[data-page=create]'))">＋ Buat PO Baru</button>
      </div>
      <div class="content">
        <div class="card">
          <div class="card-body">
            <div class="filter-row">
              <input placeholder="🔍 Cari nama pelanggan..." style="flex:1;min-width:180px">
              <select>
                <option>Semua Status</option>
                <option>Menunggu Konfirmasi</option>
                <option>Dikonfirmasi</option>
                <option>Dalam Produksi</option>
                <option>Siap Kirim</option>
                <option>Selesai</option>
                <option>Dibatalkan</option>
              </select>
              <input type="date" value="2026-04-01">
              <input type="date" value="2026-04-21">
              <button class="btn btn-primary btn-sm">Filter</button>
              <button class="btn btn-secondary btn-sm">Reset</button>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr><th>Nomor PO</th><th>Tanggal</th><th>Pelanggan</th><th>Produk Utama</th><th>Total Nilai</th><th>Tgl Kirim</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($daftar_po as $po)
                  <tr>
                    <td><span class="mono">{{ $po['nomor'] }}</span></td>
                    <td>{{ $po['tanggal'] }}</td>
                    <td>{{ $po['pelanggan'] }}</td>
                    <td>{{ $po['produk'] }}</td>
                    <td>Rp {{ number_format($po['nilai'], 0, ',', '.') }}</td>
                    <td>{{ $po['tgl_kirim'] }}</td>
                    <td>
                      @if($po['status'] == 'wait')
                        <span class="badge b-wait">⏳ Menunggu</span>
                      @elseif($po['status'] == 'conf')
                        <span class="badge b-conf">✅ Dikonfirmasi</span>
                      @elseif($po['status'] == 'prod')
                        <span class="badge b-prod">🔄 Produksi</span>
                      @elseif($po['status'] == 'ready')
                        <span class="badge b-ready">📦 Siap Kirim</span>
                      @elseif($po['status'] == 'done')
                        <span class="badge b-done">🏁 Selesai</span>
                      @else
                        <span class="badge b-cancel">✕ Dibatalkan</span>
                      @endif
                    </td>
                    <td><button class="btn btn-secondary btn-sm" onclick="alert('Detail PO belum tersedia. Silakan gunakan fitur dashboard atau hubungi admin untuk informasi lebih lanjut.')">Detail</button></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== PELANGGAN ===== -->
    <div id="page-customer" class="page">
      <div class="topbar">
        <div>
          <div class="topbar-title">Manajemen Pelanggan</div>
          <div class="topbar-subtitle">CRUD data master pelanggan · REQ-PO-026</div>
        </div>
        <button class="btn btn-primary">＋ Tambah Pelanggan</button>
      </div>
      <div class="content">
        <div class="filter-row"><input placeholder="🔍 Cari nama pelanggan..." style="max-width:280px"></div>
        <div class="cust-grid">
          @foreach($pelanggan as $cust)
          <div class="cust-card">
            <div><div class="cust-name">{{ $cust['nama'] }}</div><div class="cust-detail">📞 {{ $cust['telepon'] }}<br>✉ {{ $cust['email'] }}<br>📍 {{ $cust['alamat'] }}</div></div>
            <div class="cust-actions"><button class="btn btn-secondary btn-sm">✏ Edit</button><button class="btn btn-danger btn-sm">🗑 Hapus</button></div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- ===== PDF PREVIEW ===== -->
    <div id="page-pdf" class="page">
      <div class="topbar">
        <div>
          <div class="topbar-title">Preview &amp; Unduh PDF</div>
          <div class="topbar-subtitle">PO-20260421-003-BudiBagStore.pdf · REQ-PO-019 s/d REQ-PO-022</div>
        </div>
        <div class="topbar-right">
          <button class="btn btn-secondary">🔗 Bagikan (valid 24 jam)</button>
          <button class="btn btn-primary">⬇ Unduh PDF</button>
        </div>
      </div>
      <div class="content">
        <div class="pdf-wrap">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
            <div>
              <div class="pdf-logo">hutch.id</div>
              <div class="pdf-tag">Bag Manufacturing &amp; In-House Brand<br>Jl. Industri No. 7, Jakarta · (021) 5555-0000 · hello@hutch.id</div>
            </div>
            <div style="text-align:right">
              <div style="font-size:20px;font-weight:800;color:var(--navy)">PURCHASE ORDER</div>
              <div class="mono" style="font-size:14px">PO-20260421-003</div>
              <div style="font-size:10px;color:var(--gray)">Status: Menunggu Konfirmasi</div>
            </div>
          </div>
          <div class="pdf-divider"></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
            <div>
              <div class="pdf-section-title">Informasi PO</div>
              <div style="font-size:11.5px;line-height:1.9">
                <b>Tanggal Pesanan:</b> 21 April 2026<br>
                <b>Tanggal Kirim:</b> 28 April 2026<br>
                <b>Dibuat oleh:</b> Nayla (Staf Penjualan)
              </div>
            </div>
            <div>
              <div class="pdf-section-title">Data Pelanggan</div>
              <div style="font-size:11.5px;line-height:1.9">
                <b>Budi Bag Store</b><br>
                Jl. Sudirman No. 45, Jakarta Selatan 12190<br>
                📞 0812-3456-7890 · budi@bagstore.id
              </div>
            </div>
          </div>
          <div class="pdf-section-title" style="margin-top:14px">Detail Produk</div>
          <table class="pdf-table">
            <thead><tr><th>#</th><th>Nama Produk</th><th>Spesifikasi</th><th>Qty</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead>
            <tbody>
              <tr><td>1</td><td>Tas Kanvas Custom</td><td>Hitam, 40×30cm, logo bordir putih</td><td>50 pcs</td><td>Rp 150.000</td><td style="font-weight:700">Rp 7.500.000</td></tr>
            </tbody>
          </table>
          <div class="pdf-total"><div class="pdf-total-box">Total Nilai PO: Rp 7.500.000</div></div>
          <div class="pdf-section-title" style="margin-top:14px">Catatan Khusus</div>
          <div style="font-size:11.5px;padding:8px 12px;background:#f8fafc;border-radius:6px;border:1px solid var(--border)">
            Logo bordir di bagian depan, warna benang putih. Pastikan kualitas jahitan rapi dan seragam.
          </div>
          <div class="sign-row">
            <div class="sign-box">
              <div class="sign-line"></div>
              <div class="sign-label">Dibuat oleh — Staf Penjualan<br>Nama &amp; Tanda Tangan</div>
            </div>
            <div class="sign-box">
              <div class="sign-line"></div>
              <div class="sign-label">Disetujui oleh — Pemilik UMKM<br>Nama &amp; Tanda Tangan</div>
            </div>
          </div>
          <div class="pdf-footer">
            Halaman 1 dari 1 &nbsp;·&nbsp; Dicetak: {{ now()->format('j M Y · H:i') }} WIB &nbsp;·&nbsp;
            Dokumen ini sah sebagai bukti pesanan resmi hutch.id
          </div>
        </div>
      </div>
    </div>

</div><!-- .app -->

<script>
/* ==================== NAVIGATION ==================== */
function navigate(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('page-' + el.dataset.page).classList.add('active');
}

/* ==================== PO FORM ==================== */
let itemCount = 1;
function addItem() {
  itemCount++;
  const tbody = document.getElementById('items-tbody');
  const tr = document.createElement('tr');
  tr.id = 'item-' + itemCount;
  tr.innerHTML = `
    <td>${itemCount}</td>
    <td><select style="width:100%;min-width:140px">
      <option>Tas Kanvas Custom</option><option>Tas Punggung</option>
      <option>Tas Selempang</option><option>Dompet Kulit</option>
    </select></td>
    <td><input placeholder="Spesifikasi..." style="width:100%;min-width:120px"></td>
    <td><input type="number" value="10" min="1" style="width:65px" oninput="calcRow(${itemCount})"></td>
    <td><input value="150.000" style="width:95px" readonly class="mono"></td>
    <td style="font-weight:700;color:var(--accent)" id="sub-${itemCount}" class="mono">1.500.000</td>
    <td><button style="background:none;border:none;color:var(--red);cursor:pointer;font-size:16px;padding:0 4px" onclick="removeItem(${itemCount})">✕</button></td>
  `;
  tbody.appendChild(tr);
  calcTotal();
}

function removeItem(id) {
  const row = document.getElementById('item-' + id);
  if (row) { row.remove(); calcTotal(); }
}

function calcRow(id) {
  const row = document.getElementById('item-' + id);
  if (!row) return;
  const qty   = parseInt(row.querySelector('input[type=number]').value) || 0;
  const price = 150000;
  const sub   = qty * price;
  document.getElementById('sub-' + id).textContent = sub.toLocaleString('id-ID');
  calcTotal();
}

function calcTotal() {
  let total = 0;
  document.querySelectorAll('#items-tbody tr').forEach(row => {
    const qty   = parseInt(row.querySelector('input[type=number]')?.value) || 0;
    total += qty * 150000;
  });
  document.getElementById('total-po').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function savePO() {
  alert('PO berhasil disimpan!\n\nStatus: Menunggu Konfirmasi\nNotifikasi email telah dikirim.');
  navigate(document.querySelector('[data-page=list]'));
}

function autocomplete(val) {
  // Placeholder untuk autocomplete
}
</script>
</body>
</html>