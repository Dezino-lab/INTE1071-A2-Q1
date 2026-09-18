<?php
session_start();

echo $_SESSION['total'];

$cartData = [
    'items' => array_map(function ($item) {
        return [
            'label' => $item['name'],
            'type'  => 'LINE_ITEM',
            'price' => number_format((float)$item['price'], 2, '.', ''),
        ];
    }, $_SESSION['cart'] ?? []),
    'total' => number_format((float)($_SESSION['total'] ?? 0), 2, '.', ''),
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

        <hr>
        <div class="payment-section">
        <h2>Select Payment Option</h2>
        <div class="payment-icons">
            <button type="button">VISA</button>
            <button type="button">MasterCard</button>
            <button type="button">PayPal</button>
            <div id="gpay"></div>
        </div>
        <br>
    </div>
        <button type="submit" style="padding: 10px 20px; font-size: 16px;">Continue to checkout</button>
    </form>


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