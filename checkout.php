<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendelés Leadása</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <h1>Rendelés Leadása</h1>
    </header>

    <main class="container">
        <h2>Rendelési adatok</h2>
        <form method="post" action="place_order.php">
            <label for="name">Név:</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Cím:</label>
            <input type="text" id="address" name="address" required>

            <label for="phone">Telefonszám:</label>
            <input type="text" id="phone" name="phone" required>

            <h3>Kosár tartalma</h3>
            <div class="cart">
                <?php
                if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                    echo "<table>";
                    echo "<tr><th>Termék</th><th>Ár</th><th>Mennyiség</th><th>Összeg</th></tr>";
                    $total = 0;
                    foreach ($_SESSION['cart'] as $product_id => $product) {
                        $subtotal = $product['price'] * $product['quantity'];
                        $total += $subtotal;
                        echo "<tr>";
                        echo "<td>" . $product['name'] . "</td>";
                        echo "<td>" . $product['price'] . " Ft</td>";
                        echo "<td>" . $product['quantity'] . "</td>";
                        echo "<td>" . $subtotal . " Ft</td>";
                        echo "</tr>";
                    }
                    echo "<tr><td colspan='3'>Összesen:</td><td>" . $total . " Ft</td></tr>";
                    echo "</table>";
                } else {
                    echo "<p>A kosár üres.</p>";
                }
                ?>
            </div>

            <button type="submit">Rendelés Leadása</button>
        </form>
        <br>
        <a href="view_cart.php">Vissza a kosárhoz</a>
    </main>

    <footer class="site-footer">
        <p>&copy; 2024 Pizzéria</p>
    </footer>

</body>
</html>
