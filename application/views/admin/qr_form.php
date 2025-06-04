<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Generator</title>
</head>
<body>
<h1>QR Code Generator</h1>
<form action="/generate-qr" method="post">
    <label for="data">Enter Data:</label>
    <input type="text" name="data" id="data" required>
    <button type="submit">Generate QR Code</button>
</form>

<?php if (session()->has('qrPath')): ?>
    <h2>Generated QR Code:</h2>
    <img src="<?= base_url('uploads/qr_codes/' . basename(session('qrPath'))) ?>" alt="QR Code">
<?php endif; ?>
</body>
</html>