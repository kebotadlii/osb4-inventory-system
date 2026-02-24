@extends('layouts.app')

@section('content')
<style>
/* ================= CLICKABLE WARNING CARD (UPGRADED) ================= */
.card-clickable {
    position: relative;
    cursor: pointer;
    transition: all .28s ease;
    overflow: hidden;
}

/* lift */
.card-clickable:hover {
    transform: translateY(-5px);
}

/* hint text */
.card-clickable::after {
    content: "Klik untuk detail →";
    position: absolute;
    bottom: 12px;
    right: 16px;
    font-size: 12px;
    color: #6c757d;
    opacity: 0;
    transition: all .25s ease;
}
.card-clickable:hover::after {
    opacity: 1;
    transform: translateX(4px);
}

/* arrow icon */
.hover-icon {
    position: absolute;
    top: 14px;
    right: 16px;
    font-size: 18px;
    opacity: 0;
    transition: all .25s ease;
}
.card-clickable:hover .hover-icon {
    opacity: 1;
    transform: translateX(4px);
}

/* ===== BARANG HABIS ===== */
.card-habis {
    background: linear-gradient(
        135deg,
        rgba(220,53,69,.22),
        rgba(255,255,255,1) 70%
    );
}
.card-habis:hover {
    box-shadow:
        0 1.6rem 2.8rem rgba(0,0,0,.18),
        0 0 1.1rem rgba(220,53,69,.38);
}

/* ===== BARANG KRITIS ===== */
.card-kritis {
    background: linear-gradient(
        135deg,
        rgba(255,193,7,.28),
        rgba(255,255,255,1) 70%
    );
}
.card-kritis:hover {
    box-shadow:
        0 1.6rem 2.8rem rgba(0,0,0,.18),
        0 0 1.1rem rgba(255,193,7,.45);
}

.card-habis { border-left: 8px solid #dc3545; }
.card-kritis { border-left: 8px solid #ffc107; }

@keyframes pulse-soft {
    0%   { transform: scale(1); }
    50%  { transform: scale(1.035); }
    100% { transform: scale(1); }
}
.pulse-soft {
    animation: pulse-soft 3.6s ease-in-out infinite;
}
</style>

<div class="container-fluid py-3">

    {{-- ================= WELCOME ================= --}}
    <div class="card border-0 shadow-sm mb-4"
         style="background: linear-gradient(135deg, #0f2a44, #143d66); color:#fff; border-radius:14px;">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-semibold mb-1">Selamat Datang, {{ Auth::user()->name }}</h4>
                <small style="opacity:.85">Inventory Management System – BNI Corporate University (OSB4)</small>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2">Internal System</span>
        </div>
    </div>

    {{-- ================= SUMMARY --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted">Total Barang</span>
                    <h4 class="fw-bold mb-0">{{ number_format($totalItems) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted">Total Stok Masuk</span>
                    <h4 class="fw-bold text-success mb-0">{{ number_format($totalIn) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted">Total Stok Keluar</span>
                    <h4 class="fw-bold text-danger mb-0">{{ number_format($totalOut) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted">Kategori Barang</span>
                    <h4 class="fw-bold mb-0">{{ number_format($totalCategories) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= WARNING ================= --}}
    <div class="row g-3 mb-4">

        {{-- HABIS --}}
        <div class="col-md-6">
            <a href="{{ route('items.index', ['filter' => 'habis']) }}" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm h-100 card-clickable card-habis
                    {{ $stockHabis > 0 ? 'pulse-soft' : '' }}">
                    <div class="card-body">
                        <span class="text-muted">Barang Habis</span>
                        <span class="hover-icon text-danger">→</span>
                        <h3 class="fw-bold text-danger mb-0">{{ $stockHabis }}</h3>
                        <small class="text-muted">Stok = 0</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- KRITIS --}}
        <div class="col-md-6">
            <a href="{{ route('items.index', ['filter' => 'kritis']) }}" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm h-100 card-clickable card-kritis
                    {{ $stockKritis > 0 ? 'pulse-soft' : '' }}">
                    <div class="card-body">
                        <span class="text-muted">Barang Kritis</span>
                        <span class="hover-icon text-warning">→</span>
                        <h3 class="fw-bold text-warning mb-0">{{ $stockKritis }}</h3>
                        <small class="text-muted">Stok di bawah 10</small>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- ================= CONTENT (TIDAK DIUBAH) ================= --}}
    {{-- bagian bawah tetap sama seperti file kamu --}}
    {{-- ================= CONTENT ================= --}}
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between">
                <h6 class="mb-0 fw-semibold">Transaksi Terakhir</h6>
                <small class="text-muted">10 transaksi terbaru</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Jenis</th>
                                <th class="text-center">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestTransactions as $trx)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $trx->item->name ?? '-' }}</td>
                                    <td>{{ $trx->item->category->name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $trx->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $trx->type === 'in' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $trx->quantity }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Informasi Sistem</h6>
            </div>
            <div class="card-body small">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">✔️ Data stok realtime</li>
                    <li class="mb-2">✔️ Transaksi otomatis</li>
                    <li class="mb-2">✔️ Export laporan Excel</li>
                    <li>✔️ Monitoring internal OSB4</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
@endsection