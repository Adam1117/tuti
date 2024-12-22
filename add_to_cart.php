<?php
session_start();
include 'db_connect.php';

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Termék adatainak lekérdezése az adatbázisból
    $sql = "SELECT name, price, category_id FROM products WHERE id = $product_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $product_name = $row['name'];
        $product_price = $row['price'];
        $category_id = $row['category_id'];

        // Kosár inicializálása, ha még nincs
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Közvetlen kosárba helyezés ID 72 és ID 73 esetén
        if ($product_id == 72 || $product_id == 73) {
            // Nincs feltét vagy köret, az ID lesz az egyedi azonosító
            $unique_id = $product_id;
            if (isset($_SESSION['cart'][$unique_id])) {
                $_SESSION['cart'][$unique_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$unique_id] = array(
                    "name" => $product_name,
                    "price" => $product_price,
                    "quantity" => 1,
                    "toppings" => array() // Üres feltétlista
                );
            }
        } else {
            // Feltétek kezelésének inicializálása
            $toppings_total_price = 0;
            $toppings = array();

            // Feltétek kezelése pizzák esetén (32cm vagy 45cm)
            if ($category_id == 4 || $category_id == 5) { // Pizzák
                if (isset($_POST['topping_ids']) && !empty($_POST['topping_ids'])) {
                    $topping_ids = array_map('intval', (array) $_POST['topping_ids']);

                    // Feltétek lekérdezése
                    $sql = ($category_id == 4)
                        ? "SELECT name, price FROM toppings_32cm WHERE id IN (" . implode(',', $topping_ids) . ")"
                        : "SELECT name, price FROM toppings_45cm WHERE id IN (" . implode(',', $topping_ids) . ")";

                    $topping_result = $conn->query($sql);
                    if ($topping_result->num_rows > 0) {
                        while ($topping_row = $topping_result->fetch_assoc()) {
                            $toppings[] = array(
                                "name" => $topping_row['name'],
                                "price" => $topping_row['price']
                            );
                            $toppings_total_price += $topping_row['price'];
                        }
                    }
                }
            } elseif ($category_id == 9) { // Köret kiválasztása rántott csirkemellhez
                if (isset($_POST['koret']) && !empty($_POST['koret'])) {
                    $koret_id = intval($_POST['koret']);
                    $sql_koret = "SELECT name, price FROM sult_toppings WHERE id = $koret_id";
                    $result_koret = $conn->query($sql_koret);
                    if ($result_koret->num_rows > 0) {
                        $koret = $result_koret->fetch_assoc();
                        $toppings[] = array(
                            "name" => $koret['name'],
                            "price" => $koret['price']
                        );
                        $toppings_total_price += $koret['price'];
                    }
                } else {
                    echo "Hiba: Köret választása kötelező!";
                    exit;
                }
            } elseif ($category_id == 7 || $category_id == 8) { // Burgerek vagy üdítők
                $koret_id = isset($_POST['koret']) ? $_POST['koret'] : null;
                $udito_id = isset($_POST['udito']) ? $_POST['udito'] : null;

                if ($koret_id) {
                    $sql_koret = "SELECT name, price FROM side_dishes WHERE id = $koret_id";
                    $result_koret = $conn->query($sql_koret);
                    if ($result_koret->num_rows > 0) {
                        $koret = $result_koret->fetch_assoc();
                        $toppings[] = array(
                            "name" => $koret['name'],
                            "price" => $koret['price']
                        );
                        $toppings_total_price += $koret['price'];
                    }
                }
                if ($udito_id) {
                    $sql_udito = "SELECT name, price FROM drinks WHERE id = $udito_id";
                    $result_udito = $conn->query($sql_udito);
                    if ($result_udito->num_rows > 0) {
                        $udito = $result_udito->fetch_assoc();
                        $toppings[] = array(
                            "name" => $udito['name'],
                            "price" => $udito['price']
                        );
                        $toppings_total_price += $udito['price'];
                    }
                }
            }

            // Teljes ár kiszámítása (alap termék + feltétek ára)
            $total_price = $product_price + $toppings_total_price;

            // Egyedi azonosító létrehozása a termékhez és feltétekhez
            $unique_id = $product_id . '-' . implode(',', array_column($toppings, 'name'));

            // Ha már létezik a kosárban, növeljük a mennyiséget
            if (isset($_SESSION['cart'][$unique_id])) {
                $_SESSION['cart'][$unique_id]['quantity'] += 1;
            } else {
                // Új termék hozzáadása a kosárhoz
                $_SESSION['cart'][$unique_id] = array(
                    "name" => $product_name,
                    "price" => $total_price,
                    "quantity" => 1,
                    "toppings" => $toppings
                );
            }
        }
    }
}

// Visszairányítás az előző oldalra
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
