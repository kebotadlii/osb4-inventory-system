@extends('layouts.app')

@section('content')

<div class="container">
    <h3>Import Excel</h3>

    @if(session('success'))
        <div class="alert alert-success mt-2">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('excel.upload') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf

        <input type="file" name="file" class="form-control mb-3" required>
        <button class="btn btn-primary">Upload Excel</button>
    </form>
</div>

@endsection
