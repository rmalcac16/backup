<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de un solo uso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 40px;
            max-width: 400px;
        }

        .code {
            font-size: 48px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }

        .expiration {
            font-size: 18px;
            color: #555555;
        }
    </style>
</head>
<body>
    <div class="container">
        <p class="code">Tu código de un solo uso:</p>
        <p class="code">{{$codigo}}</p>
        <p class="expiration">Este código expirará en 30 minutos.</p>
    </div>
</body>
</html>
