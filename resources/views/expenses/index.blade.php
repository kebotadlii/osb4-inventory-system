@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Pengeluaran Operasional</h4>
            <small class="text-muted">
                Pengeluaran non-inventory & barang habis pakai
            </small>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORM INPUT --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold">Input Pengeluaran</h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('expenses.store') }}">
                @csrf

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Nama Item</label>
                        <input type="text"
                               name="item_name"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select name="expense_category_id"
                                class="form-select">
                            <option value="">-</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <input type="number"
                               name="quantity"
                               class="form-control"
                               min="1"
                               value="1"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Nominal</label>
                        <input type="number"
                               name="amount"
                               class="form-control"
                               min="0"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Penyedia</label>
                        <input type="text"
                               name="provider"
                               class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date"
                               name="expense_date"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                </div>

                <div class="text-end mt-4">
                    <button class="btn btn-primary px-4">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- CARD TOTAL --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Total Pengeluaran</small>
                    <h4 class="fw-bold mb-1">
                        Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}">
                <div class="row g-2 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="month" class="form-select">
                            <option value="">Semua</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <input type="number"
                               name="year"
                               class="form-control"
                               value="{{ request('year') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select name="expense_category_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-1">
                        <button class="btn btn-primary w-100">Terapkan</button>
                        <a href="{{ route('expenses.index') }}"
                           class="btn btn-secondary w-100">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0 table-responsive">

            <table class="table table-sm table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Kategori</th>
                        <th>Penyedia</th>
                        <th class="text-center">Qty</th>
                        <th>Nominal</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td>{{ $expense->item_name }}</td>
                            <td>{{ $expense->category?->name ?? '-' }}</td>
                            <td>{{ $expense->provider ?? '-' }}</td>
                            <td class="text-center">{{ $expense->quantity }}</td>
                            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <form action="{{ route('expenses.destroy', $expense->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="text-center text-muted py-4">
                                Belum ada data pengeluaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        {{-- PAGINATION --}}
        @if ($expenses->hasPages())
            <div class="card-footer bg-white">
                {{ $expenses->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
