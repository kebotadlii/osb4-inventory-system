<!DOCTYPE html>
<html>
<head>
    <title>Upload Excel</title>
</head>
<body>

<h2>Upload Excel / CSV</h2>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<form action="{{ route('excel.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Pilih File Excel / CSV:</label>
    <br>
    <input type="file" name="file" required>
    <br><br>

    <button type="submit">IMPORT</button>
</form>

</body>
</html>
