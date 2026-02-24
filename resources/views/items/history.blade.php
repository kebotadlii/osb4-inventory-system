@extends('layouts.app')

@section('content')

<div class="mb-4">

    {{-- TOMBOL KEMBALI DINAMIS --}}
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('items.index') }}"
       class="btn btn-secondary btn-sm mb-2">
        ← Kembali
    </a>

    <h4 class="fw-bold mb-1">{{ $item->name }}</h4>

    <small class="text-muted">
        Kategori: {{ $item->category->name ?? '-' }} |
        Stok Saat Ini:
        <strong class="{{ $item->stock <= 0 ? 'text-danger' : '' }}">
            {{ $item->stock }}
        </strong>
    </small>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th width="140">Tanggal</th>
                    <th width="100">Jenis</th>
                    <th class="text-center" width="100">Qty</th>
                    <th width="140">No PO</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr>

                        {{-- TANGGAL --}}
                        <td>
                            @if($trx->tanggal)
                                {{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- JENIS --}}
                        <td>
                            @if ($trx->type === 'in')
                                <span class="badge bg-success">IN</span>
                            @else
                                <span class="badge bg-danger">OUT</span>
                            @endif
                        </td>

                        {{-- QTY --}}
                        <td class="text-center fw-semibold">
                            {{ number_format($trx->quantity) }}
                        </td>

                        {{-- NO PO --}}
                        <td>
                            {{ $trx->no_po ?? '-' }}
                        </td>

                        {{-- KETERANGAN --}}
                        <td>
                            @php
                                $text = $trx->keterangan;
                                $hasLink = $text && preg_match('/https?:\/\/[^\s]+/i', $text);
                            @endphp

                            @if ($hasLink)
                                {!! preg_replace(
                                    '/(https?:\/\/[^\s]+)/i',
                                    '<a href="$1" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                        🔗 Link
                                    </a>',
                                    e($text)
                                ) !!}
                            @elseif (!empty($text))
                                <span class="text-muted">
                                    📝 {{ $text }}
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

<div class="mt-3">
    {{ $transactions->links() }}
</div>

@endsection