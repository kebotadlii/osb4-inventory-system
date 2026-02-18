@extends('layouts.app')

@section('title', 'Kategori Biaya')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-semibold mb-0 text-dark">
                Kategori Biaya Operasional
            </h5>
            <div class="text-muted small">
                Kelola kategori untuk pencatatan pengeluaran
            </div>
        </div>

        <a href="{{ route('expense.categories.create') }}"
           class="btn btn-sm btn-outline-primary">
            + Tambah Kategori
        </a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success small">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th class="px-3">Nama Kategori</th>
                            <th width="160" class="text-center">
                                Jumlah Data
                            </th>
                            <th width="220" class="text-end px-3">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-3">
                                    <div class="fw-medium text-dark">
                                        {{ $category->name }}
                                    </div>
                                </td>

                                <td class="text-center text-muted">
                                    {{ $category->expenses->count() ?? 0 }}
                                </td>

                                <td class="text-end px-3">
                                    <div class="d-inline-flex gap-1">

                                        {{-- IMPORT --}}
                                        <a href="{{ route('expense.categories.import.form', $category->id) }}"
                                           class="btn btn-sm btn-outline-success">
                                            Import
                                        </a>

                                        {{-- DELETE --}}
                                        <form action="{{ route('expense.categories.delete', $category->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-outline-danger">
                                                Hapus
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3"
                                    class="text-center text-muted py-4 small">
                                    Belum ada kategori biaya
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
