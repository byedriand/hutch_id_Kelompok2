@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        --primary-light: #e6f2ff;
        --primary-color: #0052a3;
        --accent-blue: #1e88e5;
        --error-color: #ef4444;
    }

    .form-header {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        animation: slideDown 0.6s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-header-content h2 {
        font-size: 2.2rem;
        font-weight: 900;
        background: linear-gradient(135deg, #0052a3, #0066cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .form-header-content p {
        color: #64748b;
        font-size: 1.05rem;
        margin: 0;
        font-weight: 500;
    }

    .form-wrapper {
        background: white;
        border-radius: 1.5rem;
        border: 1.5px solid rgba(0, 82, 163, 0.1);
        padding: 2.5rem;
        box-shadow: 0 12px 32px rgba(0, 82, 163, 0.08);
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }

    .form-section {
        margin-bottom: 2.5rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0052a3;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2.5px solid rgba(0, 102, 204, 0.15);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
    }

    .section-title::before {
        content: '';
        position: absolute;
        bottom: -2.5px;
        left: 0;
        height: 2.5px;
        background: var(--primary-gradient);
        width: 0;
        animation: expandWidth 0.6s ease-out forwards;
    }

    @keyframes expandWidth {
        to {
            width: 60px;
        }
    }

    .section-title i {
        color: #0066cc;
        font-size: 1.25rem;
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.1), rgba(0, 82, 163, 0.05));
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .form-group {
        margin-bottom: 1.75rem;
        animation: fadeInUp 0.6s ease-out;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 700;
        color: #0052a3;
        font-size: 0.95rem;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.85rem;
    }

    .form-label .required {
        color: #ef4444;
        font-weight: 900;
    }

    .form-control,
    .form-textarea {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #dbe5f1;
        border-radius: 1rem;
        font-size: 0.95rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        color: #1e293b;
        font-weight: 500;
    }

    .form-control::placeholder,
    .form-textarea::placeholder {
        color: #cbd5e1;
        font-weight: 500;
    }

    .form-control:focus,
    .form-textarea:focus {
        border-color: #0066cc;
        background: white;
        box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.12);
        outline: none;
        transform: translateY(-1px);
    }

    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-control.is-invalid,
    .form-textarea.is-invalid {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.02);
    }

    .form-control.is-invalid:focus,
    .form-textarea.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        animation: shake 0.3s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .invalid-feedback::before {
        content: '⚠';
        font-size: 1rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.75rem;
    }

    .form-actions {
        display: flex;
        gap: 1.25rem;
        justify-content: flex-start;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid rgba(0, 102, 204, 0.1);
        animation: fadeInUp 0.6s ease-out;
    }

    .btn-submit {
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-submit.primary {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 12px 30px rgba(0, 82, 163, 0.3);
    }

    .btn-submit.primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        transition: left 0.4s ease;
    }

    .btn-submit.primary:hover::before {
        left: 100%;
    }

    .btn-submit.primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px rgba(0, 82, 163, 0.4);
    }

    .btn-submit.primary:active {
        transform: translateY(-1px);
    }

    .btn-submit.secondary {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(248, 251, 255, 0.5));
        color: #64748b;
        border: 2px solid rgba(0, 102, 204, 0.2);
    }

    .btn-submit.secondary:hover {
        background: linear-gradient(135deg, #ffffff, #f8fbff);
        border-color: #0066cc;
        color: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 102, 204, 0.15);
    }

    .helper-text {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 1024px) {
        .form-header-content h2 {
            font-size: 1.8rem;
        }

        .form-wrapper {
            padding: 2rem;
        }

        .section-title {
            font-size: 1rem;
        }

        .form-control, .form-textarea {
            padding: 0.85rem 1rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 768px) {
        .form-header {
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            gap: 0.75rem;
        }

        .form-header-content h2 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .form-header-content p {
            font-size: 0.9rem;
        }

        .form-wrapper {
            padding: 1.5rem;
            border-radius: 1.25rem;
        }

        .form-section {
            margin-bottom: 1.75rem;
        }

        .section-title {
            font-size: 0.95rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .form-control, .form-textarea {
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
            border-radius: 0.85rem;
        }

        .helper-text {
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        .form-actions {
            flex-direction: row;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
        }

        .form-actions .btn-submit,
        .form-actions .btn-cancel {
            flex: 1;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .form-header {
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .form-header-content h2 {
            font-size: 1.25rem;
        }

        .form-header-content p {
            font-size: 0.85rem;
        }

        .form-wrapper {
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(219, 229, 241, 0.5);
        }

        .form-section {
            margin-bottom: 1.25rem;
        }

        .section-title {
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            padding-bottom: 0.6rem;
        }

        .section-title i {
            font-size: 1rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            font-size: 0.85rem;
            margin-bottom: 0.35rem;
            font-weight: 700;
        }

        .form-label .required {
            font-size: 0.85rem;
        }

        .form-control, .form-textarea {
            padding: 0.75rem 0.9rem;
            font-size: 0.85rem;
            border-radius: 0.8rem;
            border: 2px solid #dbe5f1;
        }

        .form-control:focus, .form-textarea:focus {
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .helper-text {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback {
            font-size: 0.75rem;
        }

        .form-actions {
            flex-direction: column;
            gap: 0.6rem;
            margin-top: 1.25rem;
            padding-top: 0.75rem;
        }

        .form-actions .btn-submit,
        .form-actions .btn-cancel {
            width: 100%;
            padding: 0.75rem 0.9rem;
            font-size: 0.85rem;
            border-radius: 0.85rem;
        }
    }
</style>

<div class="form-header">
    <div class="form-header-content">
        <h2>Tambah Pelanggan Baru</h2>
        <p>Simpan data pelanggan agar bisa dipilih saat membuat PO.</p>
    </div>
</div>

<div class="form-wrapper">
    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf

        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                Informasi Dasar Pelanggan
            </div>

            <div class="form-group">
                <label class="form-label">
                    Nama Pelanggan
                    <span class="required">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: PT. Jaya Sentosa" required>
                <div class="helper-text">Masukkan nama lengkap pelanggan atau nama perusahaan</div>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-map-location-dot"></i>
                Informasi Kontak & Alamat
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        Nomor Telepon
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-control @error('telepon') is-invalid @enderror" placeholder="0812xxxxxxx" required>
                    <div class="helper-text">Nomor telepon yang bisa dihubungi</div>
                    @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Email
                        <span class="text-muted">(Opsional)</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="email@contoh.com">
                    <div class="helper-text">Alamat email untuk komunikasi lebih lanjut</div>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Alamat Lengkap
                    <span class="required">*</span>
                </label>
                <textarea name="alamat" class="form-textarea @error('alamat') is-invalid @enderror" placeholder="Contoh: Jl. Merdeka No. 123, Jakarta Selatan" required>{{ old('alamat') }}</textarea>
                <div class="helper-text">Masukkan alamat lengkap untuk pengiriman atau referensi</div>
                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('pelanggan.index') }}" class="btn-submit secondary">
                <i class="fas fa-arrow-left"></i>Batal
            </a>
            <button type="submit" class="btn-submit primary">
                <i class="fas fa-check"></i>Simpan Pelanggan
            </button>
        </div>
    </form>
</div>
@endsection
