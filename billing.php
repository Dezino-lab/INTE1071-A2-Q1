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
            width: calc(100% - 40px);
            max-width: 1200px;
            margin: 20px auto;
            border: 1px solid #ddd;
            padding: 20px;
            box-sizing: border-box;
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
        .billing-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .billing-field {
            flex: 1;
        }
        .billing-field label {
            display: block;
            margin-bottom: 5px;
        }
        .billing-field input,
        .billing-field select {
            box-sizing: border-box;
            width: 100%;
            min-height: 34px;
        }
        .billing-field--wide {
            flex: 2;
        }
        .payment-icons {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .payment-icons form {
            margin: 0;
            flex: 0 0 150px;
            width: 150px;
            display: flex;
        }
        .payment-button {
            display: block;
            width: 150px;
            height: 70px;
            padding: 8px;
            background: white;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        .payment-button img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .payment-icons #gpay {
            flex: 0 0 150px;
            width: 150px;
        }
        @media (max-width: 600px) {
            .billing-row {
                display: block;
                margin-bottom: 0;
            }
            .billing-field {
                margin-bottom: 15px;
            }
            .payment-icons {
                justify-content: center;
            }
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
            </div>
        </div>
    </nav>
<div class="cart-container" style="font-family: sans-serif;">
    <?php if ($paymentStatus === 'success'): ?>
        <p style="padding: 12px; background: #dff0d8; color: #3c763d;">Payment completed successfully. Thank you for your order.</p>
    <?php elseif ($paymentStatus === 'square_success'): ?>
        <p style="padding: 12px; background: #dff0d8; color: #3c763d;">Square checkout completed. Thank you for your order.</p>
    <?php elseif ($paymentStatus === 'cancelled'): ?>
        <p style="padding: 12px; background: #fcf8e3; color: #8a6d3b;">Payment was cancelled. Your cart is still available.</p>
    <?php endif; ?>
    <form id="billing-form" action="billing.php" method="POST">
        
        <h2>Billing Information:</h2>
        <!-- Billing Address Section -->
        <div class="billing-row">
            <div class="billing-field">
                <label for="firstname">First name:</label>
                <input id="firstname" type="text" name="firstname" value="<?php echo htmlspecialchars($billing['firstname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="billing-field">
                <label for="lastname">Last name:</label>
                <input id="lastname" type="text" name="lastname" value="<?php echo htmlspecialchars($billing['lastname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
        </div>

        <div class="billing-row">
            <div class="billing-field">
                <label for="username">Username:</label>
                <input id="username" type="text" name="username" value="<?php echo htmlspecialchars($billing['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="billing-field">
                <label for="email">Email (Optional):</label>
                <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($billing['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <h2>Billing Address:</h2>
        <div class="billing-row">
            <div class="billing-field billing-field--wide">
                <label for="address">Address:</label>
                <input id="address" type="text" name="address" value="<?php echo htmlspecialchars($billing['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="billing-field">
                <label for="address2">Address 2 (Optional):</label>
                <input id="address2" type="text" name="address2" value="<?php echo htmlspecialchars($billing['address2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="billing-row">
            <div class="billing-field billing-field--wide">
                <label for="city">City/Suburb:</label>
                <input id="city" type="text" name="city" value="<?php echo htmlspecialchars($billing['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="billing-field">
                <label for="country">Country:</label>
                <select id="country" name="country" required>
                    <option value="" disabled <?php echo empty($billing['country']) ? 'selected' : ''; ?>>Choose...</option>
                    <option value="AU" <?php echo ($billing['country'] ?? '') === 'AU' ? 'selected' : ''; ?>>Australia</option>
                </select>
            </div>
            <div class="billing-field">
                <label for="state">State:</label>
                <select id="state" name="state" required>
                    <option value="" disabled <?php echo empty($billing['state']) ? 'selected' : ''; ?>>Choose...</option>
                    <option value="ACT" <?php echo ($billing['state'] ?? '') === 'ACT' ? 'selected' : ''; ?>>ACT</option>
                    <option value="NSW" <?php echo ($billing['state'] ?? '') === 'NSW' ? 'selected' : ''; ?>>NSW</option>
                    <option value="NT" <?php echo ($billing['state'] ?? '') === 'NT' ? 'selected' : ''; ?>>NT</option>
                    <option value="QLD" <?php echo ($billing['state'] ?? '') === 'QLD' ? 'selected' : ''; ?>>QLD</option>
                    <option value="SA" <?php echo ($billing['state'] ?? '') === 'SA' ? 'selected' : ''; ?>>SA</option>
                    <option value="TAS" <?php echo ($billing['state'] ?? '') === 'TAS' ? 'selected' : ''; ?>>TAS</option>
                    <option value="WA" <?php echo ($billing['state'] ?? '') === 'WA' ? 'selected' : ''; ?>>WA</option>
                    <option value="VIC" <?php echo ($billing['state'] ?? '') === 'VIC' ? 'selected' : ''; ?>>VIC</option>
                </select>
            </div>
            <div class="billing-field">
                <label for="zip">Zip:</label>
                <input id="zip" type="text" name="zip" value="<?php echo htmlspecialchars($billing['zip'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
        </div>

        <p>
            <input type="checkbox" id="same-address">
            <label for="same-address">Shipping address is the same as my billing address</label>
        </p>

        <p>
            <input type="checkbox" id="save-info">
            <label for="save-info">Save this information for next time</label>
        </p>

        <button type="submit" style="padding: 10px 20px; font-size: 16px;">Save Details</button>
    </form>
    <br>
</div>

    <hr>
    <div class="payment-section" style="font-family: sans-serif; margin: 20px;">
        <h2>Select Payment Option</h2>
        <div class="payment-icons">
            <form action="checkout.php" method="post" style="padding: 0; margin: 0;">
                <button type="submit" class="payment-button" aria-label="Pay with Stripe">
                    <img src="assets/img/Stripe.png" alt="Pay with Stripe">
                </button>
            </form>
            <form action="square_checkout.php" method="post" style="padding: 0; margin: 0;">
                <button type="submit" class="payment-button" aria-label="Pay with Square">
                    <img src="assets/img/square.png" alt="Pay with Square">
                </button>
            </form>
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
                <button type="submit" name="submit" class="payment-button" aria-label="Pay with PayPal">
                    <img src="assets/img/PayPal.png" alt="Pay with PayPal">
                </button>
            </form>
            <div id="gpay"></div>
        </div>
        <br>
        <form action="cart.php" method="get">
            <button type="submit" style="padding: 10px 20px; font-size: 16px;">Return to Cart</button>
        </form>
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