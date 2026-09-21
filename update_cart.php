<?php
session_start();

foreach ($_POST['quantities'] ?? [] as $index => $quantity) {
    if (!isset($_SESSION['cart'][$index])) {
        continue;
    }

    $quantity = (int) $quantity;
    if ($quantity < 1) {
        unset($_SESSION['cart'][$index]);
        continue;
    }

    $_SESSION['cart'][$index]['qty'] = $quantity;
}

$_SESSION['cart'] = array_values($_SESSION['cart'] ?? []);
header('Location: cart.php');
exit;