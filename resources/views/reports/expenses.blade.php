@extends('layouts.app')

@section('title', 'Laporan Biaya Operasional')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="fw-semibold text-dark mb-1">Laporan Biaya Operasional</h5>
            <div class="text-muted small">
                Rekap pengeluaran berdasarkan filter yang dipilih
            </div>
        </div>

        {{-- EXPORT --}}
        <a href="{{ route('reports.expenses.export', request()->query()) }}"
           class="btn btn-sm btn-success">
            ⬇️ Export Excel
        </a>
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">

                {{-- TAHUN --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-0">Tahun</label>
                    <select name="year" class="form-select form-select-sm">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}"
                                {{ (int)$year === $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- KATEGORI --}}
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-0">Kategori</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BUTTON --}}
                <div class="col-md-2">
                    <button class="btn btn-sm btn-outline-primary w-100">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="d-flex justify-content-between align-items-center px-2 py-3 mb-3 border-bottom">
        <div>
            <div class="text-muted small">Total Pengeluaran</div>
            <div class="fw-semibold fs-5 text-dark">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </div>
        </div>

        <div class="text-end small text-muted">
            Tahun {{ $year }}<br>
            {{ $categoryId ? 'Kategori terfilter' : 'Semua kategori' }}
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th width="120">Tanggal</th>
                            <th>Nama Item</th>
                            <th>Kategori</th>
                            <th width="80" class="text-center">Qty</th>
                            <th width="160" class="text-end">Total</th>
                            <th>Provider</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $exp)
                            <tr>
                                <td>{{ $exp->expense_date->format('d-m-Y') }}</td>
                                <td>{{ $exp->item_name }}</td>
                                <td>{{ $exp->category->name ?? '-' }}</td>
                                <td class="text-center">{{ $exp->quantity }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                </td>
                                <td>{{ $exp->provider ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Tidak ada data pengeluaran
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if ($expenses->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
