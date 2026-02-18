<!DOCTYPE html>
<html>
<head>
    <title>Daftar Laporan</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        th { background: #f5f5f5; }
        a.button { background: #4CAF50; padding: 8px 14px; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<h2>Daftar Laporan</h2>

<a href="{{ route('reports.importPage') }}" class="button">Import Excel Baru</a>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<table>
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Tanggal</th>
        <th>Aksi</th>
    </tr>

    @foreach($reports as $r)
    <tr>
        <td>{{ $r->id }}</td>
        <td>{{ $r->name }}</td>
        <td>{{ $r->created_at }}</td>
        <td>
            <a class="button" href="{{ route('reports.detail', $r->id) }}">Detail</a>
        </td>
    </tr>
    @endforeach
</table>

</body>
</html>
