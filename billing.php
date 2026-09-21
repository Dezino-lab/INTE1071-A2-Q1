<?php
session_start();

require_once 'config.php';

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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Information</title>
</head>
<body style="font-family: sans-serif; margin: 20px;">

    <h2>Provide Billing Information</h2>

    <form id="billing-form" action="#" method="POST">
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
            <label>Country:</label>
            <select name="country" required>
                <option value="">Choose...</option>
                <option value="AU">Australia</option>
            </select>

            <label>State:</label>
            <select name="state" required>
                <option value="">Choose...</option>
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

    <hr>
    <div class="payment-section">
        <h2>Select Payment Option</h2>
        <div class="payment-icons">
            <button type="button">VISA</button>
            <button type="button">MasterCard</button>
            <form action="<?php echo htmlspecialchars(PAYPAL_URL, ENT_QUOTES, 'UTF-8'); ?>" method="post" style="padding: 0; margin: 0;">
                <input type="hidden" name="cmd" value="_cart">
                <input type="hidden" name="upload" value="1">
                <input type="hidden" name="business" value="<?php echo htmlspecialchars(PAYPAL_ID, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="currency_code" value="<?php echo htmlspecialchars(PAYPAL_CURRENCY, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="return" value="<?php echo htmlspecialchars(PAYPAL_RETURN_URL, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="cancel_return" value="<?php echo htmlspecialchars(PAYPAL_CANCEL_URL, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="notify_url" value="<?php echo htmlspecialchars(PAYPAL_NOTIFY_URL, ENT_QUOTES, 'UTF-8'); ?>">
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