@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0">
                Import Excel – {{ $category->name }}
            </h4>
            <small class="text-muted">
                Import item & stok awal (per kategori)
            </small>
        </div>

        <div class="d-flex gap-2">
            {{-- DOWNLOAD TEMPLATE --}}
            <a href="{{ route('categories.items.import.template', $category->id) }}"
               class="btn btn-sm btn-success">
                Download Template Excel
            </a>

            <a href="{{ route('categories.items', $category->id) }}"
               class="btn btn-sm btn-secondary">
                ← Kembali ke Items
            </a>
        </div>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ALERT ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- INFO TEMPLATE --}}
            <div class="alert alert-light border mb-4">
                <strong>Petunjuk Import Excel</strong><br>
                <small class="text-muted">
                    Silakan download template Excel agar format kolom sesuai
                    dan data tidak error saat diimport.
                </small>
            </div>

            {{-- FORM IMPORT --}}
            <form action="{{ route('categories.items.import.process', $category->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="row g-3">
                @csrf

                {{-- FILE --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        File Excel <span class="text-danger">*</span>
                    </label>
                    <input type="file"
                           name="file"
                           class="form-control"
                           accept=".xlsx,.xls"
                           required>
                    <small class="text-muted">
                        Format file: .xlsx / .xls
                    </small>
                </div>

                {{-- INFO FORMAT --}}
                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0">
                        <strong>Format Kolom Excel (WAJIB URUT):</strong>
                        <ol class="mb-1">
                            <li><code>Nama Item</code></li>
                            <li><code>Jumlah</code></li>
                            <li><code>Harga</code></li>
                            <li><code>No PO</code></li>
                            <li><code>Tanggal</code> (<strong>dd/mm/yyyy</strong>)</li>
                            <li><code>Keterangan</code></li>
                        </ol>
                        <small class="text-muted">
                            * Item otomatis masuk ke kategori:
                            <strong>{{ $category->name }}</strong>
                        </small>
                    </div>
                </div>

                {{-- BUTTON --}}
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-success px-4">
                        Import Sekarang
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
