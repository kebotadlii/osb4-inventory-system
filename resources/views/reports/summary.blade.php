@extends('layouts.app')

@section('title', 'Ringkasan Laporan')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4">📊 Ringkasan Laporan</h4>

    {{-- CARD SUMMARY --}}
    <div class="row g-3 mb-4">

        {{-- TOTAL DANA DIGUNAKAN --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Dana Digunakan</h6>
                    <h4 class="fw-bold text-primary mb-0">
                        Rp {{ number_format($totalDanaDigunakan, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted">
                        Akumulasi nilai barang masuk
                    </small>
                </div>
            </div>
        </div>

        {{-- TOTAL BARANG MASUK --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Barang Masuk</h6>
                    <h4 class="fw-bold text-success mb-0">
                        Rp {{ number_format($totalBarangMasuk, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted">
                        Total nilai pembelian barang (inventory masuk)
                    </small>
                </div>
            </div>
        </div>

    </div>

    {{-- PENJELASAN --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-2">ℹ️ Keterangan</h6>
            <ul class="mb-0 text-muted">
                <li><strong>Total Dana Digunakan</strong> adalah seluruh dana yang telah dipakai untuk pembelian inventory</li>
                <li><strong>Total Barang Masuk</strong> berasal dari transaksi pembelian inventory</li>
            </ul>
        </div>
    </div>

</div>
@endsection
