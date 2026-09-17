<?php
session_start();

$id    = $_GET['id'];
$name  = $_GET['name'];
$price = $_GET['price'];

$_SESSION['cart'][] = [
    'id'    => $id,
    'name'  => $name,
    'price' => $price
];

header("Location: cart.php");
exit;
?>