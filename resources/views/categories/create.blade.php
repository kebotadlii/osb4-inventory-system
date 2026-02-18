@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="max-width: 700px">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Tambah Kategori</h4>
        <small class="text-muted">
            Form input kategori barang
        </small>
    </div>

    {{-- CARD FORM --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('categories.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Nama Kategori
                    </label>
                    <input type="text"
                           name="name"
                           class="form-control form-control-lg"
                           placeholder="Contoh: ATK, Elektronik, Furniture"
                           required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('categories.index') }}"
                       class="btn btn-secondary px-4">
                        Kembali
                    </a>

                    <button class="btn btn-primary px-4">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
