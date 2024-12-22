<?php
session_start();
include 'db_connect.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    $sql = "SELECT category_id FROM products WHERE id = $product_id";
    $result = $conn->query($sql);
    $category_id = null;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category_id = $row['category_id'];
    }

    if ($category_id == 4) {
        $sql = "SELECT id, name, price FROM toppings_32cm";
    } elseif ($category_id == 5) {
        $sql = "SELECT id, name, price FROM toppings_45cm";
    } elseif ($category_id == 7) {
        $sql_koret = "SELECT id, name, price FROM side_dishes";
        $sql_udito = "SELECT id, name, price FROM drinks";

        echo "<h4>Köret választása</h4>";
        $result_koret = $conn->query($sql_koret);
        if ($result_koret->num_rows > 0) {
            while ($row = $result_koret->fetch_assoc()) {
                $checked = ($row['id'] == 1) ? 'checked' : '';
                echo "<div>";
                echo "<input type='radio' name='koret' value='" . $row['id'] . "' $checked>";
                echo "<label>" . $row['name'] . ($row['price'] > 0 ? " (+ " . $row['price'] . " Ft)" : "") . "</label>";
                echo "</div>";
            }
        }

        echo "<h4>Üdítő választása</h4>";
        $result_udito = $conn->query($sql_udito);
        if ($result_udito->num_rows > 0) {
            while ($row = $result_udito->fetch_assoc()) {
                $checked = ($row['id'] == 1) ? 'checked' : '';
                echo "<div>";
                echo "<input type='radio' name='udito' value='" . $row['id'] . "' $checked>";
                echo "<label>" . $row['name'] . ($row['price'] > 0 ? " (+ " . $row['price'] . " Ft)" : "") . "</label>";
                echo "</div>";
            }
        }
        exit;
    } elseif ($category_id == 9) {
        $sql_koret = "SELECT id, name, price FROM sult_toppings";
        echo "<h4>Köret választása</h4>";
        $result_koret = $conn->query($sql_koret);
        if ($result_koret->num_rows > 0) {
            while ($row = $result_koret->fetch_assoc()) {
                $checked = ($row['id'] == 1) ? 'checked' : '';
                echo "<div>";
                echo "<input type='radio' name='koret' value='" . $row['id'] . "' $checked>";
                echo "<label>" . $row['name'] . ($row['price'] > 0 ? " (+ " . $row['price'] . " Ft)" : "") . "</label>";
                echo "</div>";
            }
        }
        exit;
    } elseif ($product_id == 72) {
        exit;
    }

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $price_text = $row['price'] > 0 ? " (+ " . $row['price'] . " Ft)" : "";
            echo "<div>";
            echo "<input type='checkbox' name='topping_ids[]' value='" . $row['id'] . "'>";
            echo "<label>" . $row['name'] . $price_text . "</label>";
            echo "</div>";
        }
    } else {
        echo "<p>Nincsenek elérhető feltétek ehhez a termékhez.</p>";
    }
} else {
    echo "<p>Hiba: Nincs termék ID megadva.</p>";
}
?>
