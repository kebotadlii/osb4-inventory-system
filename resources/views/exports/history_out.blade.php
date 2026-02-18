<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Item</th>
            <th>Kategori</th>
            <th>Qty</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $trx)
        <tr>
            <td>{{ $trx->tanggal }}</td>
            <td>{{ $trx->item->name }}</td>
            <td>{{ $trx->item->category->name ?? '-' }}</td>
            <td>{{ $trx->quantity }}</td>
            <td>{{ $trx->keterangan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
