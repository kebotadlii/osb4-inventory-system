@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE TITLE --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Transaksi Barang Keluar</h4>
        <small class="text-muted">
            Pencatatan pengeluaran barang inventory
        </small>
    </div>

    {{-- SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FAILED IMPORT --}}
    @if (session('failed_import'))
        @php
            $failedCount = count(session('failed_import'));
            $total = session('total_import') ?? $failedCount;
            $allFailed = $total && $failedCount === $total;
        @endphp

        <div class="alert {{ $allFailed ? 'alert-danger' : 'alert-warning' }}">
            <strong>
                {{ $allFailed
                    ? '❌ Semua data gagal di-import'
                    : '⚠️ Sebagian data gagal di-import'
                }}
            </strong>

            <p class="mb-2">
                {{ $failedCount }} dari {{ $total }} baris tidak berhasil diproses
            </p>

            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Baris</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('failed_import') as $fail)
                            <tr>
                                <td>{{ $fail['row'] }}</td>
                                <td>{{ $fail['item'] ?? '-' }}</td>
                                <td>{{ $fail['qty'] ?? '-' }}</td>
                                <td class="text-danger fw-semibold">
                                    {{ $fail['reason'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ERROR VALIDATION --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- IMPORT EXCEL --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Import Barang Keluar (Excel)</h6>

                <a href="{{ route('transactions.out.import.template') }}"
                   class="btn btn-sm btn-outline-secondary">
                    ⬇️ Download Template
                </a>
            </div>

            <form method="POST"
                  action="{{ route('transactions.out.import') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="row g-3 align-items-end">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Upload File Excel
                        </label>
                        <input type="file"
                               name="file"
                               class="form-control"
                               accept=".xlsx,.xls,.csv"
                               required>
                        <small class="text-muted">
                            Kolom: Item | Qty | Tanggal | Keterangan
                        </small>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-outline-danger w-100">
                            Import Excel
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- FORM MANUAL --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <form method="POST" action="{{ route('transactions.out.store') }}">
                @csrf

                <div class="row g-3">

                    {{-- NAMA BARANG --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <select name="item_id"
                                id="item_id"
                                class="form-select @error('item_id') is-invalid @enderror"
                                required>
                            <option value=""></option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} | Stok: {{ $item->stock }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <input type="number"
                               name="quantity"
                               class="form-control"
                               min="1"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Keterangan / Link Bukti
                        </label>
                        <input type="text"
                               name="keterangan"
                               class="form-control"
                               placeholder="Pengeluaran proyek A / link bukti">
                    </div>

                </div>

                <div class="text-end mt-4">
                    <button class="btn btn-danger px-4">
                        Terapkan
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">

            <h6 class="fw-bold mb-3">Riwayat Barang Keluar</h6>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Qty</th>
                        <th>Bukti / Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($transactions as $trx)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $trx->tanggal->format('d-m-Y') }}</td>
                        <td>{{ $trx->item->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-danger">{{ $trx->quantity }}</span>
                        </td>
                        <td>
                            @if($trx->keterangan && Str::startsWith($trx->keterangan, ['http://', 'https://']))
                                <a href="{{ $trx->keterangan }}" target="_blank">
                                    Lihat Bukti
                                </a>
                            @else
                                {{ $trx->keterangan ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada transaksi
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $transactions->links() }}

        </div>
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
