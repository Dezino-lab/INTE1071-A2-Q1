<?php
session_start();

require_once 'config.php';

$billing = $_SESSION['billing'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billing = [
        'firstname' => trim($_POST['firstname'] ?? ''),
        'lastname'  => trim($_POST['lastname'] ?? ''),
        'username'  => trim($_POST['username'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'address'   => trim($_POST['address'] ?? ''),
        'address2'  => trim($_POST['address2'] ?? ''),
        'city'      => trim($_POST['city'] ?? ''),
        'country'   => trim($_POST['country'] ?? ''),
        'state'     => trim($_POST['state'] ?? ''),
        'zip'       => trim($_POST['zip'] ?? ''),
    ];
    $_SESSION['billing'] = $billing;
}

$cartItems = $_SESSION['cart'] ?? [];
$cartTotal = 0;
foreach ($cartItems as $item) {
    $quantity = max(1, (int) ($item['qty'] ?? 1));
    $cartTotal += (float) $item['price'] * $quantity;
}
$_SESSION['total'] = $cartTotal;

$cartData = [
    'items' => array_map(function ($item) {
        return [
            'label' => $item['name'],
            'type'  => 'LINE_ITEM',
            'price' => number_format((float)$item['price'], 2, '.', ''),
        ];
    }, $_SESSION['cart'] ?? []),
    'total' => number_format($cartTotal, 2, '.', ''),
];

$paymentStatus = $_GET['payment'] ?? '';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Information</title>
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
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
            </div>
        
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
                <form class="navbar-form navbar-right" role="search">
                    <div class="form-group">
                        <input type="text" placeholder="Enter Keyword Here ..." class="form-control">
                    </div>
                    &nbsp; 
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
    </nav>
<div class="cart-container" style="font-family: sans-serif; margin: 20px;">
    <?php if ($paymentStatus === 'success'): ?>
        <p style="padding: 12px; background: #dff0d8; color: #3c763d;">Payment completed successfully. Thank you for your order.</p>
    <?php elseif ($paymentStatus === 'square_success'): ?>
        <p style="padding: 12px; background: #dff0d8; color: #3c763d;">Square checkout completed. Thank you for your order.</p>
    <?php elseif ($paymentStatus === 'cancelled'): ?>
        <p style="padding: 12px; background: #fcf8e3; color: #8a6d3b;">Payment was cancelled. Your cart is still available.</p>
    <?php endif; ?>
    <form id="billing-form" action="billing.php" method="POST">
        
        <h2>Provide Billing Information</h2>
        <!-- Billing Address Section -->
        <h3>Billing Address</h3>
        
        <p>
            <label>First name:</label><br>
            <input type="text" name="firstname" required>
        </p>

        <p>
            <label>Last name:</label><br>
            <input type="text" name="lastname" required>
        </p>

        <p>
            <label>Username:</label><br>
            <input type="text" name="username"required>
        </p>

        <p>
            <label>Email (Optional):</label><br>
            <input type="email" name="email">
        </p>

        <p>
            <label>Address:</label><br>
            <input type="text" name="address" required>
        </p>

        <p>
            <label>Address 2 (Optional):</label><br>
            <input type="text" name="address2" >
        </p>

        <p>
            <label>City/Suburb:</label><br>
            <input type="text" name="city" required>
        </p>

        <p>
            <label>Country:</label>
            <select name="country" required placeholder="Choose...">
                <option value="" disabled selected>Choose...</option>
                <option value="AU">Australia</option>
            </select>

            <label>State:</label>
            <select name="state" required placeholder="Choose...">
                <option value="" disabled selected>Choose...</option>
                <option value="ACT">ACT</option>
                <option value="NSW">NSW</option>
                <option value="NT">NT</option>
                <option value="QLD">QLD</option>
                <option value="SA">SA</option>
                <option value="TAS">TAS</option>
                <option value="WA">WA</option>
                <option value="VIC">VIC</option>
            </select>

            <label>Zip:</label>
            <input type="text" name="zip" size="10" required>
        </p>

        <p>
            <input type="checkbox" id="same-address">
            <label for="same-address">Shipping address is the same as my billing address</label>
        </p>

        <p>
            <input type="checkbox" id="save-info">
            <label for="save-info">Save this information for next time</label>
        </p>

        <button type="submit" style="padding: 10px 20px; font-size: 16px;">Continue to checkout</button>
    </form>
    <br>
    <form action="cart.php" method="get">
            <button type="submit" style="padding: 10px 20px; font-size: 16px;">Return to Cart</button>
    </form>
</div>

    <hr>
    <div class="payment-section" style="font-family: sans-serif; margin: 20px;">
        <h2>Select Payment Option</h2>
        <div class="payment-icons">
            <form action="checkout.php" method="post" style="padding: 0; margin: 0;">
                <button type="submit">Pay with Stripe</button>
            </form>
            <form action="square_checkout.php" method="post" style="padding: 0; margin: 0;">
                <button type="submit">Pay with Square</button>
            </form>
            <button type="button">MasterCard</button>
            <form action="<?php echo htmlspecialchars(PAYPAL_URL, ENT_QUOTES, 'UTF-8'); ?>" method="post" style="padding: 0; margin: 0;">
                <input type="hidden" name="cmd" value="_cart">
                <input type="hidden" name="upload" value="1">
                <input type="hidden" name="business" value="<?php echo htmlspecialchars(PAYPAL_ID, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="currency_code" value="<?php echo htmlspecialchars(PAYPAL_CURRENCY, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="return" value="<?php echo htmlspecialchars(PAYPAL_RETURN_URL, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="cancel_return" value="<?php echo htmlspecialchars(PAYPAL_CANCEL_URL, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="notify_url" value="<?php echo htmlspecialchars(PAYPAL_NOTIFY_URL, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="address_override" value="1">
                <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($billing['firstname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($billing['lastname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($billing['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="address1" value="<?php echo htmlspecialchars($billing['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="address2" value="<?php echo htmlspecialchars($billing['address2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($billing['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="state" value="<?php echo htmlspecialchars($billing['state'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="zip" value="<?php echo htmlspecialchars($billing['zip'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="country" value="<?php echo htmlspecialchars($billing['country'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="custom" value="<?php echo htmlspecialchars(json_encode([
                    'username' => $billing['username'] ?? '',
                    'address2' => $billing['address2'] ?? '',
                ], JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                <?php foreach ($cartItems as $index => $item): ?>
                    <input type="hidden" name="item_name_<?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="item_number_<?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="amount_<?php echo $index + 1; ?>" value="<?php echo number_format((float) $item['price'], 2, '.', ''); ?>">
                    <input type="hidden" name="quantity_<?php echo $index + 1; ?>" value="<?php echo max(1, (int) ($item['qty'] ?? 1)); ?>">
                <?php endforeach; ?>
                <button type="submit" name="submit">Pay with PayPal</button>
            </form>
            <div id="gpay"></div>
        </div>
        <br>
    </div>

<script>
  window.cartData = <?php echo json_encode($cartData,
      JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script>
    window.cartTotal = <?php echo json_encode((float) $_SESSION['total']); ?>;
</script>

<!-- Local Google Pay Script -->
<script src="gpay.js"></script>
<!-- Google Pay JavaScript Library -->
<script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>

</body>
</html>