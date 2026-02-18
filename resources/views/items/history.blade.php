@extends('layouts.app')

@section('content')

<div class="mb-4">
    <a href="{{ route('items.all') }}" class="btn btn-secondary btn-sm mb-2">
        ← Kembali ke Daftar Item
    </a>

    <h4 class="fw-bold mb-1">{{ $item->name }}</h4>
    <small class="text-muted">
        Kategori: {{ $item->category->name ?? '-' }} |
        Stok Saat Ini: <strong>{{ $item->stock }}</strong>
    </small>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th class="text-center">Qty</th>
                    <th>No PO</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}
                        </td>

                        <td>
                            @if ($trx->type === 'in')
                                <span class="badge bg-success">IN</span>
                            @else
                                <span class="badge bg-danger">OUT</span>
                            @endif
                        </td>

                        <td class="text-center">
                            {{ number_format($trx->quantity) }}
                        </td>

                        <td>
                            {{ $trx->no_po ?? '-' }}
                        </td>

                        <td>
                            @php
                                $text = $trx->keterangan;
                                $hasLink = $text && preg_match('/https?:\/\/[^\s]+/i', $text);
                            @endphp

                            @if ($hasLink)
                                {!! preg_replace(
                                    '/(https?:\/\/[^\s]+)/i',
                                    '<a href="$1" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                        <i class="fas fa-link me-1"></i>Link
                                    </a>',
                                    e($text)
                                ) !!}
                            @elseif (!empty($text))
                                <span class="text-muted">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    {{ $text }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada transaksi untuk item ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
