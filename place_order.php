<?php
session_start();
include 'db_connect.php';  // Csatlakozás az adatbázishoz

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Rendelési adatok
    $name = $_POST['name'];
    $order_number = $_POST['order_number'];
    $order_hsz = $_POST['order_hsz'];
    $utca = $_POST['utca'];
    $hsz = $_POST['hsz'];
    $mj = $_POST['mj'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $total_amount = 0;

    // Kosár összegének kiszámítása
    foreach ($_SESSION['cart'] as $product) {
        $total_amount += $product['price'] * $product['quantity'];
    }

    // Kiszállítási díj
    $delivery_fee = 0;  // Állítsd be a szállítási díjat
    $total_amount += $delivery_fee;  // Hozzáadjuk a végösszeghez

    // Rendelés mentése az adatbázisba
    $ref_no = mt_rand(100000000000, 999999999999);  // Random rendelési szám generálása
    $date_created = date('Y-m-d H:i:s');  // Rendelési dátum
    $status = 'pending';  // Kezdeti státusz

    $order_sql = "INSERT INTO orders (ref_no, total_amount, order_number, order_hsz, date_created, utca, hsz, mj, tel, name, status, email)
                  VALUES ('$ref_no', $total_amount, '$order_number', '$order_hsz', '$date_created', '$utca', '$hsz', '$mj', '$tel', '$name', '$status', '$email')";

    if ($conn->query($order_sql) === TRUE) {
        $order_id = $conn->insert_id;  // Rendelés ID

        // Kosár tételeinek mentése az order_items táblába
        foreach ($_SESSION['cart'] as $unique_id => $product) {
            // Kivesszük az első részt az egyedi azonosítóból, amely a product_id
            $product_id_parts = explode('-', $unique_id);
            $product_id = intval($product_id_parts[0]);  // Csak az első részt, a numerikus product_id-t használjuk
            $quantity = $product['quantity'];
            $price = $product['price'];
            $amount = $quantity * $price;

            if (is_numeric($product_id)) {
                // Tétel mentése az order_items táblába
                $item_sql = "INSERT INTO order_items (order_id, product_id, qty, price, amount)
                             VALUES ($order_id, $product_id, $quantity, $price, $amount)";
                if ($conn->query($item_sql)) {
                    $order_item_id = $conn->insert_id;  // Az aktuális tétel ID-ja

                    // Feltétek mentése az order_toppings táblába
                    if (isset($product['toppings']) && !empty($product['toppings'])) {
                        foreach ($product['toppings'] as $topping) {
                            $topping_sql = "INSERT INTO order_toppings (order_item_id, topping_name)
                                            VALUES ($order_item_id, '" . $conn->real_escape_string($topping['name']) . "')";
                            $conn->query($topping_sql);
                        }
                    }
                }
            }
        }

        // Kosár ürítése
        unset($_SESSION['cart']);  // Ez törli a kosár tartalmát a session-ből

        // Visszaigazolás
        header('Location: order_confirmation.php?order_id=' . $order_id);
    } else {
        echo "Hiba történt a rendelés leadása során: " . $conn->error;
    }

    $conn->close();  // Kapcsolat lezárása
}
?>
