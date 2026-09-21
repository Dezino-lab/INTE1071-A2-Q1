<?php
session_start();

$id    = $_GET['id'] ?? '';
$name  = $_GET['name'] ?? '';
$price = (float) ($_GET['price'] ?? 0);

if (!empty($_SESSION['cart'])) {
    $matchingIndex = null;
    $quantity = 1;

    foreach ($_SESSION['cart'] as $index => $item) {
        if ((string) $item['id'] !== (string) $id) {
            continue;
        }

        if ($matchingIndex === null) {
            $matchingIndex = $index;
            $quantity += max(1, (int) ($item['qty'] ?? 1));
        } else {
            $quantity += max(1, (int) ($item['qty'] ?? 1));
            unset($_SESSION['cart'][$index]);
        }
    }

    if ($matchingIndex !== null) {
        $_SESSION['cart'][$matchingIndex]['qty'] = $quantity;
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        header('Location: cart.php');
        exit;
    }
}

$_SESSION['cart'][] = [
    'id'    => $id,
    'name'  => $name,
    'price' => $price,
    'qty'   => 1
];

header("Location: cart.php");
exit;
?>