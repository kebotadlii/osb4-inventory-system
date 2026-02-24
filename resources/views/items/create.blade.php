@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-1">Tambah Item</h3>
            <p class="text-muted mb-0">
                Tambah data item baru ke sistem inventory
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('items.index') }}" class="btn btn-secondary btn-sm">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- ALERT ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Gagal menyimpan data:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('items.store') }}" method="POST">
                @csrf

                {{-- KATEGORI --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Kategori <span class="text-danger">*</span>
                    </label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- NAMA ITEM --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nama Item <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Contoh: Kabel LAN"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                {{-- HARGA --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Harga (Opsional)
                    </label>
                    <input
                        type="number"
                        name="price"
                        class="form-control"
                        placeholder="Contoh: 15000"
                        value="{{ old('price') }}"
                        min="0"
                    >
                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('items.index') }}" class="btn btn-light">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Item
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection