<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étel házhozszállítás</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('dream-pulse-header.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .delivery-section {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px 20px;
            border-radius: 10px;
            margin: 40px auto;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .delivery-section h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .delivery-section table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            color: #333;
            text-align: left;
        }

        .delivery-section th, .delivery-section td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .delivery-section th {
            background-color: #ffcc00;
            color: #333;
        }

        .back-to-home {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #ffcc00;
            color: #333;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            width: 200px;
            cursor: pointer;
        }

        .back-to-home:hover {
            background-color: #e6b800;
        }
    </style>
</head>
<body>
    <div class="delivery-section">
        <h2>Étel házhozszállítási területek</h2>
        <p>Az alábbi táblázat tartalmazza a kiszállítási területeket és a hozzájuk tartozó szállítási díjakat:</p>
        <table>
            <thead>
                <tr>
                    <th>Település</th>
                    <th>Szállítási díj</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Hosszúhetény</td>
                    <td>Ingyenes</td>
                </tr>
                <tr>
                    <td>Pécs-Hird</td>
                    <td>500 Ft</td>
                </tr>
                <tr>
                    <td>Pécs-Vasas</td>
                    <td>800 Ft</td>
                </tr>
                <tr>
                    <td>Pécsvárad</td>
                    <td>600 Ft</td>
                </tr>
                <tr>
                    <td>Püspükszentlászló</td>
                    <td>200 Ft</td>
                </tr>
            </tbody>
        </table>

        <a href="index.php" class="back-to-home">Vissza a főoldalra</a>
    </div>
</body>
</html>
