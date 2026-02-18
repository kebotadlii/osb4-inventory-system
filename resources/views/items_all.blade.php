@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Manajemen Item</h4>
            <small class="text-muted">
                Kelola master data inventory
            </small>
        </div>

        <a href="/items/create" class="btn btn-primary">
            + Tambah Item
        </a>
    </div>

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Item</div>
                    <div class="fs-4 fw-bold">
                        {{ $totalItems }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Stok Habis</div>
                    <div class="fs-4 fw-bold text-danger">
                        {{ $stokHabis }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Stok Kritis (&lt; 10)</div>
                    <div class="fs-4 fw-bold text-warning">
                        {{ $stokKritis }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="/items"
                  class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label small">Nama Item</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari nama item..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Status Stok</label>
                    <select name="filter" class="form-select">
                        <option value="">Semua</option>
                        <option value="habis" {{ request('filter') == 'habis' ? 'selected' : '' }}>
                        Habis
                        </option>
                        <option value="kritis" {{ request('filter') == 'kritis' ? 'selected' : '' }}>
                         Kritis
                    </option>

                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">
                        Terapkan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="px-3 py-2 border-bottom fw-semibold text-muted">
            Daftar Item Inventory
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Item</th>
                        <th>Kategori</th>
                        <th class="text-center">Status Stok</th>
                        <th class="text-end">Harga Terakhir</th>
                        <th>No PO</th>
                        <th>Tanggal</th>
                        <th class="text-end">Total Nilai</th>
                        <th class="text-center" width="200">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($items as $item)

                    @php
                        $lastIn = $item->transactions
                            ->where('type', 'in')
                            ->sortByDesc('tanggal')
                            ->first();

                        $price = $lastIn->price ?? 0;
                        $noPo  = $lastIn->no_po ?? '-';
                        $date  = $lastIn?->tanggal?->format('d/m/Y') ?? '-';
                        $total = $item->stock * $price;
                    @endphp

                    <tr
                        @class([
                            'table-danger' => $item->stock <= 0,
                            'table-warning' => $item->stock > 0 && $item->stock < 10,
                        ])
                    >
                        <td>
                            {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                        </td>

                        <td class="fw-semibold">
                            {{ $item->name }}
                        </td>

                        <td>
                            {{ $item->category->name ?? '-' }}
                        </td>

                        <td class="text-center">
                            @if($item->stock <= 0)
                                <span class="badge bg-danger">Habis</span>
                            @elseif($item->stock < 10)
                                <span class="badge bg-warning text-dark">
                                    Kritis ({{ $item->stock }})
                                </span>
                            @else
                                <span class="badge bg-success">
                                    Aman ({{ $item->stock }})
                                </span>
                            @endif
                        </td>

                        <td class="text-end">
                            Rp {{ number_format($price, 0, ',', '.') }}
                        </td>

                        <td>{{ $noPo }}</td>
                        <td>{{ $date }}</td>

                        <td class="text-end fw-bold">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="/items/{{ $item->id }}/history"
                                   class="btn btn-sm btn-outline-primary">
                                    History
                                </a>

                                <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editItem{{ $item->id }}">
                                    Edit
                                </button>

                                <form action="/items/{{ $item->id }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin hapus item ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="9"
                            class="text-center text-muted py-4">
                            Data item tidak ditemukan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
            <small class="text-muted">
                Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
                dari {{ $items->total() }} data
            </small>

            <div class="pagination pagination-sm mb-0">
                {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>

{{-- MODAL EDIT --}}
@foreach($items as $item)
<div class="modal fade"
     id="editItem{{ $item->id }}"
     tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="/items/{{ $item->id }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Item</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ $item->name }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number"
                               name="price"
                               class="form-control"
                               value="{{ $item->price }}"
                               min="0">
                    </div>

                    <small class="text-muted">
                        * Stok hanya berubah lewat transaksi masuk / keluar
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
@endsection
