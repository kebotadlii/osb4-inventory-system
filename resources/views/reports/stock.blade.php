@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Laporan Stok</h4>
        <div class="text-muted small">
            Ringkasan stok dan nilai barang —
            <span class="fw-semibold">
                {{ $year === 'all' ? 'Semua Waktu' : 'Tahun ' . $year }}
            </span>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Periode</label>
                    <select name="year" class="form-select">
                        <option value="all" {{ $year === 'all' ? 'selected' : '' }}>
                            Semua Waktu
                        </option>
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        Terapkan
                    </button>
                    <a href="{{ route('reports.stock') }}"
                       class="btn btn-outline-secondary ms-2">
                        Reset
                    </a>
                    <a href="{{ route('reports.stock.export', request()->query()) }}"
                       class="btn btn-success ms-2">
                        Export Excel
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- RINGKASAN CARD --}}
    <div class="row g-3 mb-4">

        {{-- BARANG MASUK --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1">Total Nilai Barang Masuk</div>
                    <h5 class="fw-bold mb-1">
                        Rp {{ number_format($totalInValue, 0, ',', '.') }}
                    </h5>
                    <div class="small text-muted border-top pt-1">
                        {{ $year === 'all' ? 'Semua Waktu' : 'Tahun ' . $year }}
                    </div>
                </div>
            </div>
        </div>

        {{-- STOK --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-1">Total Stok Saat Ini</div>
                    <h5 class="fw-bold mb-1">
                        {{ number_format($totalStockQty) }} unit
                    </h5>
                    <div class="small text-muted border-top pt-1">
                        Posisi stok real (semua waktu)
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-end">Masuk</th>
                        <th class="text-end">Keluar</th>
                        <th class="text-end">Stok Saat Ini</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Total Nilai</th>
                        <th class="text-center">Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $items->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td class="text-end">{{ number_format($item->stock_in) }}</td>
                        <td class="text-end">{{ number_format($item->stock_out) }}</td>
                        <td class="text-end fw-bold">{{ number_format($item->stock) }}</td>
                        <td class="text-end">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-semibold">
                            Rp {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('items.history', $item->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Data stok belum tersedia
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Menampilkan {{ $items->firstItem() ?? 0 }}
            – {{ $items->lastItem() ?? 0 }}
            dari {{ $items->total() }} data
        </div>
        {{ $items->links() }}
    </div>

</div>
@endsection
