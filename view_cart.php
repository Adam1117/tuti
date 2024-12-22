<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosár</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Kosár tartalma</h1>
    </header>

    <main>
        <div class="cart-content">
            <?php
            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                echo "<table>";
                echo "<tr><th>Termék</th><th>Ár</th><th>Mennyiség</th><th>Összeg</th><th>Művelet</th></tr>";
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $product) {
                    $subtotal = $product['price'] * $product['quantity'];
                    $total += $subtotal;
                    echo "<tr>";
                    echo "<td>" . $product['name'];

                    // Feltétek, köretek és üdítők megjelenítése a termék alatt
                    if (!empty($product['toppings'])) {
                        echo "<br><small>Feltétek: ";
                        $topping_details = [];
                        foreach ($product['toppings'] as $topping) {
                            $topping_details[] = $topping['name'] . " (+ " . $topping['price'] . " Ft)";
                        }
                        echo implode(', ', $topping_details);
                        echo "</small>";
                    }

                    echo "</td>";
                    echo "<td>" . number_format($product['price'], 0, ',', ' ') . " Ft</td>";
                    echo "<td>" . $product['quantity'] . "</td>";
                    echo "<td>" . number_format($subtotal, 0, ',', ' ') . " Ft</td>";
                    echo "<td><a href='remove_from_cart.php?product_id=" . urlencode($product_id) . "'>Törlés</a></td>";
                    echo "</tr>";
                }
                echo "<tr class='total-row'><td colspan='3'>Összesen:</td><td colspan='2'>" . number_format($total, 0, ',', ' ') . " Ft</td></tr>";
                echo "</table>";
            } else {
                echo "<p>A kosár üres.</p>";
            }
            ?>
        </div>

        <div class="checkout">
            <a href="checkout.php">Tovább a fizetéshez</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Pizzéria</p>
    </footer>

</body>
</html>
