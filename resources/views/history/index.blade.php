@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">History Transaksi</h4>
            <small class="text-muted">
                Riwayat barang masuk dan keluar
            </small>
        </div>

        {{-- EXPORT --}}
        @php
            $typeSelected = request()->filled('type');
        @endphp

        <a href="{{ $typeSelected ? route('history.export', request()->all()) : '#' }}"
           class="btn btn-success btn-sm {{ !$typeSelected ? 'disabled' : '' }}"
           title="{{ !$typeSelected ? 'Pilih jenis transaksi terlebih dahulu' : '' }}">
            <i class="fa fa-file-excel"></i> Export Excel
        </a>
    </div>

    {{-- ALERT ERROR --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- INFO --}}
    <div class="alert alert-info py-2 mb-3">
        <i class="fa fa-info-circle me-1"></i>
        Silakan pilih <strong>jenis transaksi</strong> terlebih dahulu untuk melakukan export.
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                {{-- BULAN --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Bulan</label>
                    <select name="month" class="form-select">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}"
                                {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TAHUN --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Tahun</label>
                    <select name="year" class="form-select">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}"
                                {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- JENIS --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Jenis <span class="text-danger">*</span>
                    </label>
                    <select name="type" class="form-select">
                        <option value="">Pilih Jenis</option>
                        <option value="in" {{ request('type')=='in'?'selected':'' }}>Barang Masuk</option>
                        <option value="out" {{ request('type')=='out'?'selected':'' }}>Barang Keluar</option>
                    </select>
                </div>

                {{-- KATEGORI --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BUTTON --}}
                <div class="col-12 text-end">
                    <button class="btn btn-primary px-4">Terapkan</button>
                    <a href="{{ route('history.index') }}" class="btn btn-outline-secondary ms-2">
                        Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th width="120">Tanggal</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th class="text-center" width="120">Jenis</th>
                            <th class="text-center" width="80">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td>
                                    {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                                </td>
                                <td>{{ $trx->tanggal->format('d-m-Y') }}</td>
                                <td class="fw-semibold">{{ $trx->item->name ?? '-' }}</td>
                                <td>{{ $trx->item->category->name ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $trx->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $trx->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $trx->quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada data history
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
                <small class="text-muted">
                    Menampilkan
                    <strong>{{ $transactions->firstItem() ?? 0 }}</strong>
                    –
                    <strong>{{ $transactions->lastItem() ?? 0 }}</strong>
                    dari
                    <strong>{{ $transactions->total() }}</strong>
                    data
                </small>

                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
@endsection
