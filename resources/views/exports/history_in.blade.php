<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Item</th>
            <th>Kategori</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
            <th>No PO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $trx)
        <tr>
            <td>{{ $trx->tanggal }}</td>
            <td>{{ $trx->item->name }}</td>
            <td>{{ $trx->item->category->name ?? '-' }}</td>
            <td>{{ $trx->quantity }}</td>
            <td>{{ $trx->price }}</td>
            <td>{{ $trx->total }}</td>
            <td>{{ $trx->no_po }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
