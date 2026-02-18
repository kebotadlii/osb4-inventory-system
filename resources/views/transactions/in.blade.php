@extends('layouts.app')

@section('header')
<h4 class="mb-0 fw-semibold">Transaksi Barang Masuk</h4>
<small class="text-muted">Pencatatan barang masuk berdasarkan PO</small>
@endsection

@section('content')

<div class="container-fluid">

    {{-- ALERT --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FORM --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold">Input Barang Masuk</h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('transactions.in.store') }}">
                @csrf

                <div class="row g-3">

                    {{-- NAMA BARANG --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <select name="item_id"
                                id="item_id"
                                class="form-select"
                                required>
                            <option value=""></option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} | Stok: {{ $item->stock }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">No PO</label>
                        <input type="text"
                               name="no_po"
                               class="form-control"
                               placeholder="PO-2025-001"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <input type="number"
                               name="quantity"
                               class="form-control"
                               min="1"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Harga</label>
                        <input type="number"
                               name="price"
                               class="form-control"
                               min="0"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Keterangan / Bukti (Opsional)
                        </label>
                        <input type="text"
                               name="keterangan"
                               class="form-control"
                               placeholder="Nota / Link Drive"
                               value="{{ old('keterangan') }}">
                        <small class="text-muted">
                            Bisa diisi keterangan singkat atau link bukti
                        </small>
                    </div>

                </div>

                <div class="text-end mt-4">
                    <button class="btn btn-primary">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h6 class="mb-0 fw-semibold">Riwayat Barang Masuk</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>No PO</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Total</th>
                            <th>Bukti / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $trx)
                            <tr>
                                <td>
                                    {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                                </td>
                                <td>{{ $trx->tanggal->format('d-m-Y') }}</td>
                                <td>{{ $trx->item->name ?? '-' }}</td>
                                <td>{{ $trx->no_po }}</td>
                                <td class="text-center">{{ $trx->quantity }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($trx->price, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($trx->keterangan && Str::startsWith($trx->keterangan, ['http://', 'https://']))
                                        <a href="{{ $trx->keterangan }}" target="_blank">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            {{ $trx->keterangan ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada transaksi barang masuk
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($transactions->hasPages())
            <div class="card-footer bg-white">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

{{-- ===== TOM SELECT ===== --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new TomSelect('#item_id', {
        placeholder: 'Ketik nama barang...',
        allowEmptyOption: true,
        maxOptions: 10,
        closeAfterSelect: true
    });
});
</script>
@endpush
