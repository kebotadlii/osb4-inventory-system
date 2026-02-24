@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h4 class="fw-bold">Edit Item</h4>
        <small class="text-muted">Perbarui data barang</small>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('items.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- KATEGORI --}}
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- NAMA ITEM --}}
                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $item->name) }}"
                           required>
                </div>

                {{-- HARGA --}}
                <div class="mb-3">
                    <label class="form-label">Harga (opsional)</label>
                    <input type="number"
                           name="price"
                           class="form-control"
                           value="{{ old('price', $item->price) }}"
                           min="0">
                </div>

                <div class="d-flex justify-content-between">

                    <a href="{{ route('items.index') }}"
                       class="btn btn-outline-secondary">
                        ← Kembali
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection