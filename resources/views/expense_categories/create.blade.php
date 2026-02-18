@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <h4 class="fw-bold mb-3">Tambah Kategori Biaya</h4>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form method="POST" action="{{ route('expense.categories.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           placeholder="Contoh: Air Galon"
                           required>
                </div>

                <div class="text-end">
                    <a href="{{ route('expense.categories.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>
                    <button class="btn btn-success px-4">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
