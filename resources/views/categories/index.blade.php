@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Kategori Barang</h4>
        <small class="text-muted">
            Master data kategori inventory
        </small>
    </div>

    {{-- SUMMARY --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Kategori</div>
                    <div class="fs-3 fw-bold">
                        {{ $categories->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div class="fw-semibold">
                Daftar Kategori
            </div>

            <a href="{{ route('categories.create') }}"
               class="btn btn-primary btn-sm">
                + Tambah Kategori
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama Kategori</th>
                            <th width="160" class="text-center">Jumlah Item</th>
                            <th width="140" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- ✅ KATEGORI BISA DIKLIK --}}
                                <td class="fw-semibold">
                                    <a href="{{ route('categories.items', $cat->id) }}"
                                       class="text-decoration-none">
                                        {{ $cat->name }}
                                    </a>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        {{ $cat->items_count ?? $cat->items->count() }}
                                    </span>
                                </td>

                                {{-- ✅ TOMBOL JELAS --}}
                                <td class="text-center">
                                    <a href="{{ route('categories.items', $cat->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Lihat Item
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="text-center text-muted py-4">
                                    Data kategori belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection
