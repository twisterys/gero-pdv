<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Code Barre</title>
    <style>
        .barcode {
            text-align: left;
            margin-top: 50px;
        }

        .barcode img {
            width: 300px;
            height: auto;
        }
    </style>
</head>
<body>
<div class="barcode">
    <img src="data:image/png;base64,{{ $barcode }}" alt="Code Barre">
    <p>{{ $code }}</p>
</div>
</body>
</html>
