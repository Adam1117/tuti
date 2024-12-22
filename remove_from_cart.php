<?php
session_start();

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Ellenőrizzük, hogy a kosárban van-e a termék
    if (isset($_SESSION['cart'][$product_id])) {
        // Termék eltávolítása a kosárból
        unset($_SESSION['cart'][$product_id]);
    }
}

// Visszatérés a kosár nézetéhez
header('Location: index.php'); // Vagy ahova vissza szeretnél irányítani
exit();
?>
