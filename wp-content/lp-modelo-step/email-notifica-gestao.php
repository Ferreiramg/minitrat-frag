<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nava Solicitação Pelo Site</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo svg {
            max-width: 100%;
            height: auto;
        }

        .message {
            line-height: 1.6;
        }

        .signature {
            margin-top: 20px;
            font-style: italic;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            color: #333333;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img width="150" height="120" src="https://minitrat.com.br/wp-includes/app/assets/mail-logo.png" class="thumb-img" alt="Minitrat by ECTAS e ACQUALIMP">
        </div>
        <div class="message">
            <p><strong>Nova Solicitação</strong></p>

            <?= $tabelaHTML ?>

        </div>

        <div class="signature">
            <p>
                <em>Atenciosamente,<br />Equipe Minitrat</em>
            </p>
            <p>
                <em>Email automatico</em>
            </p>
        </div>
    </div>

</body>

</html>