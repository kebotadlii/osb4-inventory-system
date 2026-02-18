@extends('layouts.app')

@section('content')

<h3>Semua Barang</h3>

<table class="table table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->category->name ?? '-' }}</td>
            <td>
                <span class="badge bg-info">
                    {{ $item->stock }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
