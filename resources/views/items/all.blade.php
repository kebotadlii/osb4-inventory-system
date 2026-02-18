@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold">Semua Barang</h4>
    <small class="text-muted">Daftar seluruh item dari semua kategori</small>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">

        <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                {{ $item->stock }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Data item kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
