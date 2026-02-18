@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Import Data dari Excel</h4>
        <small class="text-muted">
            Import master item + stok awal (otomatis tercatat di history)
        </small>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('import.process') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                {{-- FILE --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        File Excel <span class="text-danger">*</span>
                    </label>
                    <input type="file"
                           name="file"
                           class="form-control"
                           accept=".xlsx,.xls"
                           required>
                </div>

                {{-- INFO FORMAT --}}
                <div class="alert alert-info py-2">
                    <i class="fa fa-info-circle me-1"></i>
                    Format kolom Excel:
                    <strong>
                        Nama Barang | Kategori | Stok Awal | Harga |
                        No PO | Tanggal (dd/mm/yyyy) | Keterangan
                    </strong>
                </div>

                {{-- ACTION --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('import.template') }}"
                       class="btn btn-outline-secondary">
                        Download Template
                    </a>

                    <button type="submit"
                            class="btn btn-success px-4">
                        Import Sekarang
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
