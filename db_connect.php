<?php
// Adatbázis kapcsolódás
$conn = new mysqli('localhost', 'root', '', 'cafe_billing_db');


// Hibakezelés
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}
?>
