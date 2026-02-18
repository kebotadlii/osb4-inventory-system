@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">
                Import Biaya Operasional
            </h4>
            <small class="text-muted">
                Kategori: <strong>{{ $category->name }}</strong>
            </small>
        </div>

        <a href="{{ route('expense.categories.index') }}" class="btn btn-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- ALERT ERROR --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- DOWNLOAD TEMPLATE --}}
            <div class="mb-3">
                <a href="{{ route('expense.categories.import.template', $category->id) }}"
                   class="btn btn-outline-primary btn-sm">
                    ⬇ Download Template Excel
                </a>
            </div>

            {{-- FORM IMPORT --}}
            <form action="{{ route('expense.categories.import.process', $category->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        File Excel (.xlsx / .xls)
                    </label>
                    <input type="file"
                           name="file"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-success">
                    🚀 Import Sekarang
                </button>
            </form>

        </div>
    </div>

    {{-- INFO FORMAT --}}
    <div class="card mt-3 border-0 bg-light">
        <div class="card-body">
            <h6 class="fw-bold mb-2">📌 Format Kolom Excel (WAJIB)</h6>
            <ol class="mb-0 small">
                <li><strong>Tanggal</strong> (dd/mm/yyyy)</li>
                <li><strong>Nama Item</strong></li>
                <li><strong>No Invoice</strong></li>
                <li><strong>Provider</strong></li>
                <li><strong>Jumlah</strong></li>
                <li><strong>Nominal</strong></li>
            </ol>
            <small class="text-muted d-block mt-2">
                * Kategori <strong>{{ $category->name }}</strong> otomatis diambil dari halaman ini
            </small>
        </div>
    </div>

</div>
@endsection
