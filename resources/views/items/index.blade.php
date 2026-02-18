@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0">
                Item Kategori: {{ $category->name }}
            </h4>
            <small class="text-muted">
                Monitoring stok barang per kategori
            </small>
        </div>

        {{-- ACTION --}}
        <div class="d-flex gap-2">
            {{-- ⬆️ IMPORT EXCEL --}}
            <a href="{{ route('categories.items.import.form', $category->id) }}"
               class="btn btn-sm btn-success">
                <i class="fa fa-file-excel"></i> Import Excel
            </a>

            <a href="{{ route('categories.index') }}"
               class="btn btn-sm btn-secondary">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- CARD --}}
    <div class="card border-0 shadow-sm">

        {{-- INFO --}}
        <div class="card-body border-bottom d-flex justify-content-between align-items-center">
            <small class="text-muted">
                🔴 Habis | 🟡 Kritis (&lt; 10) | 🟢 Aman
            </small>
        </div>

        {{-- TABLE --}}
        <div class="card-body table-responsive p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Barang</th>
                        <th class="text-center" width="140">Status Stok</th>
                        <th class="text-center" width="100">Jumlah</th>
                        <th class="text-end" width="160">Harga</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($items as $item)
                    @php
                        $stock = $item->stock;

                        if ($stock <= 0) {
                            $status = 'HABIS';
                            $badge = 'danger';
                            $rowBg = 'rgba(220,53,69,.05)';
                        } elseif ($stock < 10) {
                            $status = 'KRITIS';
                            $badge = 'warning';
                            $rowBg = 'rgba(255,193,7,.08)';
                        } else {
                            $status = 'AMAN';
                            $badge = 'success';
                            $rowBg = 'transparent';
                        }
                    @endphp

                    <tr style="background-color: {{ $rowBg }}">
                        <td>{{ $loop->iteration }}</td>

                        <td class="fw-semibold">
                            {{ $item->name }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-{{ $badge }}">
                                {{ $status }}
                            </span>
                        </td>

                        <td class="text-center fw-semibold">
                            {{ $stock }}
                        </td>

                        <td class="text-end">
                            @if(!empty($item->price) && $item->price > 0)
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            @else
                                <span class="text-muted fst-italic">
                                    Belum ditetapkan
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <a href="#"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="text-center text-muted py-4">
                            Belum ada item di kategori ini
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>
@endsection
