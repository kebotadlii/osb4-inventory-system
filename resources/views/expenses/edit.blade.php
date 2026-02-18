@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <h4 class="fw-bold mb-3">Edit Biaya Operasional</h4>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Item</label>
                        <input type="text" name="item_name"
                               class="form-control"
                               value="{{ old('item_name', $expense->item_name) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="expense_category_id" class="form-select" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $expense->expense_category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="expense_date"
                               class="form-control"
                               value="{{ $expense->expense_date }}"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Qty</label>
                        <input type="number" name="quantity"
                               class="form-control"
                               value="{{ $expense->quantity }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nominal</label>
                        <input type="number" name="amount"
                               class="form-control"
                               value="{{ $expense->amount }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Penyedia</label>
                        <input type="text" name="provider"
                               class="form-control"
                               value="{{ $expense->provider }}">
                    </div>

                </div>

                <div class="mt-4">
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
