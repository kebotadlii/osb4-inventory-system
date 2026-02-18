@extends('layouts.app')

@section('title', 'Import Biaya')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="mb-4">
        <h5 class="fw-semibold text-dark mb-1">
            Import Data Biaya
        </h5>
        <div class="text-muted small">
            Kategori: <strong>{{ $category->name }}</strong>
        </div>
    </div>

    {{-- PETUNJUK --}}
    <div class="alert alert-light border small mb-4">
        Gunakan template Excel agar format data sesuai sistem.
        <br>
        Pastikan data diisi dengan benar sebelum di-upload.
    </div>

    {{-- TEMPLATE + DOWNLOAD --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold mb-0">
                    Format Kolom File
                </h6>

                <a href="{{ route('expense.categories.import.template', $category->id) }}"
                   class="btn btn-sm btn-outline-success">
                    📥 Download Template Excel
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th>Tanggal</th>
                            <th>Nama Item</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th>Provider</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="small">
                            <td>2025-01-15</td>
                            <td>Kertas A4</td>
                            <td>10</td>
                            <td>5000</td>
                            <td>Toko ATK Jaya</td>
                            <td>Pembelian rutin</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-muted small mt-2">
                * Format tanggal wajib: <code>YYYY-MM-DD</code>
            </div>
        </div>
    </div>

    {{-- FORM IMPORT --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('expense.categories.import.process', $category->id) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label small text-muted">
                        Upload File Excel / CSV
                    </label>
                    <input type="file"
                           name="file"
                           class="form-control form-control-sm"
                           required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expense.categories.index') }}"
                       class="btn btn-sm btn-outline-secondary">
                        ← Kembali
                    </a>

                    <button type="submit" class="btn btn-sm btn-primary">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
