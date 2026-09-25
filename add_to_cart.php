<?php
session_start();

$id    = $_GET['id'] ?? '';
$name  = $_GET['name'] ?? '';
$price = (float) ($_GET['price'] ?? 0);
$descriptions = [
    '001' => "750W Motor, 52V Battery, Range: 60 miles, Top Speed: 32 mph\n\nAvailability: Online Immediate|Pick-up",
    '002' => "500W Motor, 48V Battery, Range: 50 miles, Top Speed: 28 mph\n\nAvailability: Online Immediate|Pick-up",
    '003' => "300W Motor, 36V Battery, Range: 35 miles, Top Speed: 30 mph\n\nAvailability: Online Immediate|Pick-up",
];
$description = $descriptions[(string) $id] ?? 'Description unavailable.';

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
        $_SESSION['cart'][$matchingIndex]['description'] = $description;
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        header('Location: cart.php');
        exit;
    }
}

$_SESSION['cart'][] = [
    'id'    => $id,
    'name'  => $name,
    'price' => $price,
    'description' => $description,
    'qty'   => 1
];

header("Location: cart.php");
exit;
?>