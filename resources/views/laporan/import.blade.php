<!DOCTYPE html>
<html>
<head>
    <title>Import Excel</title>
    <style>
        button { background:#2196F3; color:white; border:none; padding:8px 15px; }
    </style>
</head>
<body>

<h2>Import Excel</h2>

<a href="{{ route('reports.index') }}">← Kembali</a>

@if($errors->any())
<p style="color:red;">{{ $errors->first() }}</p>
@endif

<form method="POST" action="{{ route('reports.import') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel_file" required><br><br>
    <button>Upload</button>
</form>

</body>
</html>
