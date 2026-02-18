<!DOCTYPE html>
<html>
<head>
    <title>Detail Laporan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
        td.editable:hover { background:#f5f5f5; cursor:pointer; }
        button { padding:6px 10px; }
    </style>
</head>
<body>

<h2>Detail: {{ $report->name }}</h2>

<a href="{{ route('reports.index') }}">← Kembali</a>

<br><br>

<form action="{{ route('reports.delete', $report->id) }}" method="POST" onsubmit="return confirm('Yakin hapus laporan?')">
    @csrf
    @method('DELETE')
    <button style="background:red; color:white;">Hapus Laporan</button>
</form>

<table>
    <thead>
        <tr>
            @foreach($titles as $t)
                <th>{{ $t->title }}</th>
            @endforeach
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        @foreach($rows->groupBy('row_number') as $rowNum => $rowData)
        <tr>
            @foreach($titles as $col)
                @php
                    $value = $rowData->where('report_title_id', $col->id)->first();
                @endphp
                <td class="editable" data-id="{{ $value->id ?? '' }}">
                    {{ $value->value ?? '' }}
                </td>
            @endforeach

            <td>
                <form method="POST" action="{{ route('reports.deleteRow', [$report->id, $rowNum]) }}" onsubmit="return confirm('Hapus baris ini?')">
                    @csrf
                    @method('DELETE')
                    <button style="background:red; color:white;">Hapus Baris</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
document.querySelectorAll('.editable').forEach(cell => {

    cell.addEventListener('click', function () {
        let id = this.dataset.id;
        if (!id) return;

        let oldVal = this.innerText;
        let input = document.createElement("input");
        input.value = oldVal;
        input.style.width = "100%";

        this.innerHTML = "";
        this.appendChild(input);
        input.focus();

        input.addEventListener("blur", () => {
            fetch("{{ route('reports.updateCell') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    row_id: id,
                    value: input.value
                })
            });

            this.innerHTML = input.value;
        });
    });
});
</script>

</body>
</html>
