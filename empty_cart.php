<?php
session_start();

// Kosár ürítése
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}