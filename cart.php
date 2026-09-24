<?php
session_start();

$_SESSION['total'] = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $_SESSION['total'] += (float) $item['price'] * max(1, (int) ($item['qty'] ?? 1));
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Cart</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Fontawesome core CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
    <!--GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <!--Slide Show Css -->
    <link href="assets/ItemSlider/css/main-style.css" rel="stylesheet" />
    <!-- custom CSS here -->
    <link href="assets/css/style.css" rel="stylesheet" />
    
    <style>
        .cart-container {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .cart-header, .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .cart-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .cart-item img {
            max-width: 100px;
        }
        .payment-icons img {
            width: 100px;
            height: 50px;
            object-fit: contain;
            margin-right: 10px;
        }
        .cart-item p {
            margin: 0;
        }
        .cart-item .description {
            flex: 2;
            padding: 0 10px;
        }
        .cart-item .price, .cart-item .qty, .cart-item .total {
            flex: 1;
            text-align: center;
        }
        .cart-item .qty input {
            width: 50px;
            text-align: center;
        }
        .update-btn, .remove-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Login</a></li>
                    <li><a href="#">Signup</a></li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">24x7 Support <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><strong>Call: </strong>+61-000-000-000</a></li>
                            <li><a href="#"><strong>Mail: </strong>info@alicebikeshop.com</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><strong>Address: </strong>
                                <div>
                                    Melbourne,<br />
                                    VIC 3000, AUSTRALIA
                                </div>
                            </a></li>
                        </ul>
                    </li>
                </ul> 
            </div>
            <!-- /.navbar-collapse -->
        </div>
                <!-- /.container-fluid -->
    </nav>
<div class="cart-container">
    <h1>Shopping Cart:</h1>
    <form action="update_cart.php" method="post">
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Description</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Remove</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $index => $item) {
                    $quantity = max(1, (int) ($item['qty'] ?? 1));
                    $lineTotal = (float) $item['price'] * $quantity;
                    echo "<tr>";
                    echo "<td><img src='assets/img/{$item['name']}.jpg' width='150'></td>";
                    echo "<td>{$item['name']}</td>";
                    echo "<td>ID: {$item['id']}</td>";
                    echo "<td>\${$item['price']}</td>";
                    echo "<td><input type='number' name='quantities[{$index}]' value='{$quantity}' min='1' class='qty-input'></td>";
                    echo "<td>\$" . number_format($lineTotal, 2) . "</td>";
                    echo "<td><a href='remove_from_cart.php?index={$index}' class='remove-btn'>REMOVE</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Your cart is empty.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-primary">CONTINUE SHOPPING</a>
    <button type="submit" class="btn btn-primary">UPDATE QTY</button>
    <a href="remove_from_cart.php?clear=1" class="btn btn-primary">CLEAR CART</a>
</form>
    

    <div class="cart-total">
        <h4>Total: $ <?php echo number_format($_SESSION['total'], 2); ?></h4>
    </div>

    <div class="payment-section">
        <h3>Available Payment Options:</h3>
        <div class="payment-icons">
            <img src="assets/img/square.png" alt="Square">
            <img src="assets/img/stripe-power.svg" alt="Stripe">
            <img src="assets/img/PayPal-icon.png" alt="PayPal">
            <img src="assets/img/gpay.png" alt="Google Pay">
        </div>
    </div>
    <br>
    <div class="checkout-footer">
        <form action="billing.php" method="get">
            <button type="submit" class="btn btn-primary">CHECKOUT NOW</button>
        </form>

    </div>
</div>

<!-- Pass PHP total into a global JavaScript variable -->
<script>
  window.cartTotalFromPHP = "<?php echo number_format($_SESSION['total'], 2, '.', ''); ?>";
</script>

<script>
  window.cartData = <?php echo json_encode([
      'items' => array_map(function ($item) {
          return [
              'label' => $item['name'],
              'type'  => 'LINE_ITEM',
              'price' => number_format((float)$item['price'], 2, '.', ''),
          ];
      }, $_SESSION['cart'] ?? []),
      'total' => number_format((float)($_SESSION['total'] ?? 0), 2, '.', ''),
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
</body>
</html>
