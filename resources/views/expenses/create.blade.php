@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-3">
        <h4 class="fw-bold mb-0">Tambah Biaya Operasional</h4>
        <small class="text-muted">Input pengeluaran non-inventory</small>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form method="POST" action="{{ route('expenses.store') }}">
                @csrf

                <div class="row g-3">

                    {{-- NAMA ITEM --}}
                    <div class="col-md-4">
                        <label class="form-label">Nama Item</label>
                        <input type="text"
                               name="item_name"
                               class="form-control"
                               value="{{ old('item_name') }}"
                               required>
                    </div>

                    {{-- KATEGORI --}}
                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select name="expense_category_id" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- NO INVOICE --}}
                    <div class="col-md-3">
                        <label class="form-label">No Invoice</label>
                        <input type="text"
                               name="invoice_number"
                               class="form-control"
                               value="{{ old('invoice_number') }}">
                    </div>

                    {{-- PENYEDIA --}}
                    <div class="col-md-3">
                        <label class="form-label">Nama Penyedia</label>
                        <input type="text"
                               name="provider"
                               class="form-control"
                               value="{{ old('provider') }}">
                    </div>

                    {{-- QTY --}}
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="number"
                               name="quantity"
                               class="form-control"
                               value="{{ old('quantity') }}">
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date"
                               name="expense_date"
                               class="form-control"
                               value="{{ old('expense_date', date('Y-m-d')) }}"
                               required>
                    </div>

                    {{-- NOMINAL --}}
                    <div class="col-md-3">
                        <label class="form-label">Total Biaya</label>
                        <input type="number"
                               name="amount"
                               class="form-control"
                               value="{{ old('amount') }}"
                               required>
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
