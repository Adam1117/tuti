<?php
// Kosár tartalom megjelenítése
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<table>";
    echo "<tr><th>Termék</th><th>Ár</th><th>Mennyiség</th><th>Összeg</th><th>Művelet</th></tr>";
    $total = 0;
    foreach ($_SESSION['cart'] as $product_id => $product) {
        $subtotal = $product['price'] * $product['quantity'];
        $total += $subtotal;
        echo "<tr>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $product['price'] . " Ft</td>";
        echo "<td>" . $product['quantity'] . "</td>";
        echo "<td>" . $subtotal . " Ft</td>";
        echo "<td><a href='javascript:void(0);' onclick='removeFromCart(" . $product_id . ");'>Törlés</a></td>";
        echo "</tr>";
    }
    echo "<tr><td colspan='3'>Összesen:</td><td colspan='2'>" . $total . " Ft</td></tr>";
    echo "</table>";
} else {
    echo "<p>A kosár üres.</p>";
}
?>