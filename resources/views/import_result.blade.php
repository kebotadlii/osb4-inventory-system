@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="fw-bold mb-0">Hasil Import Data</h4>
                    <small class="text-muted">Ringkasan hasil proses import Excel</small>
                </div>
            </div>
        </div>
    </div>

    <!-- RESULT -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    @php $results = $results ?? []; @endphp

                    @if (count($results) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Barang</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="20%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $index => $row)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $row['item_name'] ?? '-' }}</td>
                                        <td>{{ $row['quantity'] ?? '-' }}</td>
                                        <td>
                                            @if (($row['status'] ?? '') === 'success')
                                                <span class="badge bg-success">Berhasil</span>
                                            @else
                                                <span class="badge bg-danger">Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Tidak ada data hasil import untuk ditampilkan.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
