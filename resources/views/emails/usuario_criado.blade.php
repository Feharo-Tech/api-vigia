<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciais de acesso ao API Vigia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        h1 {
            color: #3c9ae7;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .alert-container {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .alert-container p {
            margin: 10px 0;
        }

        .alert-container strong {
            font-weight: bold;
            color: #2c3e50;
        }

        .alert-details {
            background-color: #f9f9f9;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
        }

        .alert-details p {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .alert-details p:last-child {
            border-bottom: none;
        }

        .alert-footer {
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .footer-link {
            color: #3498db;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 20px;
            }

            .alert-container {
                padding: 15px;
            }

            .alert-details p {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="alert-container">
        <h1>Credenciais de acesso ao API Vigia</h1>

        <div class="alert-details">
            <p><strong>E-mail:</strong> {{ $email }}</p>
            <p><strong>Senha Temporaria:</strong> {{ $senha }}</p>
        </div>

        <p class="text-red-600"> Você deve alterar a senha temporária assim que fizer o login.</p>

        <div class="alert-footer">
            <p>Para fazer login, acesse <a href="{{ route('welcome') }}" class="footer-link">o painel de controle</a>.
            </p>
        </div>
    </div>
</body>

</html>