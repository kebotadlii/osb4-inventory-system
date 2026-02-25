@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="d-flex align-items-center gap-3">

            {{-- BACK UNIVERSAL --}}
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
               class="btn btn-outline-secondary btn-sm">
                ← Kembali
            </a>

            @isset($category)
                <a href="{{ route('categories.index') }}"
                   class="btn btn-outline-secondary btn-sm">
                    ← Kategori
                </a>
            @endisset

            <div>
                <h4 class="fw-bold mb-0">
                    @isset($category)
                        Item Kategori: {{ $category->name }}
                    @else
                        Semua Item
                    @endisset
                </h4>
                <small class="text-muted">
                    Monitoring stok barang
                </small>
            </div>

        </div>

        <div class="d-flex gap-2 align-items-center">

            {{-- DOWNLOAD TEMPLATE --}}
            @isset($category)
                <a href="{{ route('categories.items.import.template', $category->id) }}"
                   class="btn btn-outline-success btn-sm">
                    Download Template
                </a>
            @else
                <a href="{{ route('items.import.template') }}"
                   class="btn btn-outline-success btn-sm">
                    Download Template
                </a>
            @endisset


            {{-- IMPORT --}}
            <form action="{{ isset($category) 
                                ? route('categories.items.import.process', $category->id) 
                                : route('items.import') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="d-flex gap-2 align-items-center">
                @csrf
                <input type="file"
                       name="file"
                       class="form-control form-control-sm"
                       accept=".xlsx,.xls,.csv"
                       required>
                <button type="submit" class="btn btn-success btn-sm">
                    Import
                </button>
            </form>

            {{-- TAMBAH --}}
            <a href="{{ route('items.create') }}"
               class="btn btn-primary">
                + Tambah Item
            </a>

        </div>

    </div>

    {{-- ================= FILTER & SEARCH ================= --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">

            <form method="GET"
                  action="{{ isset($category) 
                                ? route('categories.items', $category->id) 
                                : route('items.index') }}"
                  class="row g-2">

                <div class="col-md-4">
                    <select name="search"
                            id="search_item"
                            class="form-select">
                        <option value="">Ketik nama barang...</option>

                        @php
                            $searchItems = $allItems ?? $items;
                        @endphp

                        @foreach($searchItems as $itemOption)
                            <option value="{{ $itemOption->name }}"
                                {{ request('search') == $itemOption->name ? 'selected' : '' }}>
                                {{ $itemOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @unless(isset($category))
                <div class="col-md-3">
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
                @endunless

                <div class="col-md-3">
                    <select name="filter" class="form-select">
                        <option value="">Semua Status</option>
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

    {{-- ================= TABLE ================= --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body table-responsive p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Barang</th>

                        @unless(isset($category))
                            <th>Kategori</th>
                        @endunless

                        <th class="text-center" width="120">Status</th>
                        <th class="text-center" width="90">Stok</th>
                        <th class="text-end" width="140">Harga</th>
                        <th class="text-end" width="160">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($items as $item)

                    @php
                        $stock = $item->stock;

                        if ($stock <= 0) {
                            $status = 'HABIS';
                            $badge = 'danger';
                            $rowClass = 'table-danger';
                        } elseif ($stock < 10) {
                            $status = 'KRITIS';
                            $badge = 'warning';
                            $rowClass = 'table-warning';
                        } else {
                            $status = 'AMAN';
                            $badge = 'success';
                            $rowClass = '';
                        }
                    @endphp

                    <tr class="{{ $rowClass }}">
                        <td>
                            {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                        </td>

                        <td class="fw-semibold">
                            {{ $item->name }}
                        </td>

                        @unless(isset($category))
                            <td>{{ $item->category->name ?? '-' }}</td>
                        @endunless

                        <td class="text-center">
                            <span class="badge bg-{{ $badge }}">
                                {{ $status }}
                            </span>
                        </td>

                        <td class="text-center fw-semibold">
                            {{ $stock }}
                        </td>

                        <td class="text-end">
                            @if($item->price > 0)
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            @else
                                <span class="text-muted fst-italic">
                                    Belum ditetapkan
                                </span>
                            @endif
                        </td>

                        <td class="text-end">
                            <div class="d-inline-flex gap-1">

                                <a href="{{ route('items.history', $item->id) }}"
                                   class="btn btn-sm btn-outline-info">
                                    History
                                </a>

                                <a href="{{ route('items.edit', $item->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

                                @if($stock == 0)
                                    <form action="{{ route('items.destroy', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin hapus item ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        Hapus
                                    </button>
                                @endif

                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $items->withQueryString()->links() }}
        </div>

    </div>

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new TomSelect('#search_item', {
        placeholder: 'Ketik nama barang...',
        allowEmptyOption: true,
        maxOptions: 10,
        closeAfterSelect: true
    });
});
</script>
@endpush