<?php
include 'db_connect.php';

// PHPMailer fájlok betöltése
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$order_id = null;

// Ellenőrizzük, hogy az order_id át lett-e adva
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Rendelési adatok lekérése
    $result = $conn->query("SELECT * FROM orders WHERE id = $order_id");
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Szállítási díj meghatározása
        $delivery_fee = 0;
        if ($order['order_hsz'] === "Komló") {
            $delivery_fee = 200;
        } elseif ($order['order_hsz'] === "Pécs-Hird") {
            $delivery_fee = 500;
        } elseif ($order['order_hsz'] === "Pécs-Vasas") {
            $delivery_fee = 800;
        } elseif ($order['order_hsz'] === "Hosszúhetény") {
            $delivery_fee = 0; // Ingyenes
        }

        // Rendelési tételek összegyűjtése
        $items_result = $conn->query("
            SELECT o.*, p.name AS product_name, 
                   GROUP_CONCAT(t.topping_name SEPARATOR ', ') AS toppings 
            FROM order_items o 
            INNER JOIN products p ON p.id = o.product_id 
            LEFT JOIN order_toppings t ON t.order_item_id = o.id 
            WHERE o.order_id = $order_id 
            GROUP BY o.id
        ");

        $items_list = "";
        while ($row = $items_result->fetch_assoc()) {
            $toppings = !empty($row['toppings']) ? " ({$row['toppings']})" : ""; // Ha nincs feltét, nem jelenik meg
            $items_list .= "{$row['product_name']} ({$row['qty']} db) {$toppings} - " . number_format($row['amount'], 2) . " Ft<br>";
        }

        // Végösszeg számítása
        $total_amount = $order['total_amount'] + $delivery_fee;

        // E-mail küldése
        $customer_email = $order['email'];
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.zoho.eu';
            $mail->SMTPAuth = true;
            $mail->Username = 'tutifalatozo@zohomail.eu';
            $mail->Password = 'BEnett22';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('tutifalatozo@zohomail.eu', 'Tuti Falatozó');
            $mail->addAddress($customer_email);

           $mail->isHTML(true);
$mail->Subject = "Rendelés visszaigazolás - Tuti Falatozó";
$mail->Body = "
    <div style='font-family: Arial, sans-serif; line-height: 1.5; color: #333;'>
        <h2>Köszönjük a rendelését!</h2>
        <p>Az alábbi rendelést rögzítettük:</p>
        <h4>Vevői adatok:</h4>
        <p><b>Név:</b> {$order['name']}<br>
        <b>Telefonszám:</b> {$order['tel']}<br>
        <b>Email:</b> {$order['email']}<br>
        <b>Cím:</b> {$order['order_hsz']} , {$order['utca']} {$order['hsz']}, </p>
        <h4>Rendelési tételek:</h4>
        <table style='width: 100%; border-collapse: collapse;'>
            <thead>
                <tr style='background-color: #f8f8f8;'>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Termék</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Db</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Feltétek</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Ár</th>
                </tr>
            </thead>
            <tbody>
                {$items_list}
            </tbody>
        </table>
        <h4>Összesítés:</h4>
        <p><b>Termékek összesen:</b> " . number_format($order['total_amount'], 2) . " Ft<br>
        <b>Szállítási díj:</b> " . number_format($delivery_fee, 2) . " Ft<br>
        <b>Végösszeg:</b> " . number_format($total_amount, 2) . " Ft</p>
        <p>Köszönjük, hogy minket választott!</p>
    </div>
";
            $mail->send();
        } catch (Exception $e) {
            error_log("Hiba történt az e-mail küldése közben: {$mail->ErrorInfo}");
        }
    } else {
        $order = null;
    }
} else {
    $order = null;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendelési visszaigazolás</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .navbar {
            background-color: #ffcc00;
            padding: 10px 20px;
            text-align: center;
            color: #333;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .details-table th {
            background-color: #ffcc00;
            color: #333;
            font-weight: bold;
        }

        .details-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .summary {
            margin: 20px 0;
            text-align: right;
            font-size: 18px;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ffcc00;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .button:hover {
            background-color: #e6b800;
        }
    </style>
</head>
<body>
    <header class="navbar">
        Rendelési visszaigazolás
    </header>
    <main class="container">
        <?php if ($order): ?>
            <h2>Köszönjük a rendelését!</h2>
            <p>Az alábbi rendelést rögzítettük:</p>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Termék</th>
                        <th>Db</th>
                        <th>Feltétek</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items_result as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= htmlspecialchars($row['qty']) ?></td>
                        <td><?= htmlspecialchars($row['toppings'] ?: '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
           
            <a href="index.php" class="button">Vissza a kezdőlapra</a>
        <?php else: ?>
            <h2>Hiba történt</h2>
            <p>A rendelés nem található!</p>
            <a href="index.php" class="button">Vissza a kezdőlapra</a>
        <?php endif; ?>
    </main>
</body>
</html>
