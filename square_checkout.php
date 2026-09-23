<?php
session_start();

require_once __DIR__ . '/secrets.php';

$squareAccessToken = getenv('SQUARE_ACCESS_TOKEN') ?: ($squareAccessToken ?? '');
$squareLocationId = getenv('SQUARE_LOCATION_ID') ?: ($squareLocationId ?? '');

if (!$squareAccessToken || !$squareLocationId) {
    http_response_code(500);
    exit('Square is not configured. Set SQUARE_ACCESS_TOKEN and SQUARE_LOCATION_ID.');
}

if (str_starts_with($squareAccessToken, 'sandbox-sq0idb-')) {
    http_response_code(500);
    exit('SQUARE_ACCESS_TOKEN contains a Square Application ID. Replace it with the Sandbox Access Token from the Credentials page.');
}

$cartItems = $_SESSION['cart'] ?? [];
if (!$cartItems) {
    header('Location: cart.php');
    exit;
}

$billing = $_SESSION['billing'] ?? [];

$lineItems = [];
foreach ($cartItems as $item) {
    $price = (float) ($item['price'] ?? 0);
    $quantity = max(1, (int) ($item['qty'] ?? 1));

    if ($price <= 0 || empty($item['name'])) {
        continue;
    }

    $lineItems[] = [
        'name' => (string) $item['name'],
        'quantity' => (string) $quantity,
        'base_price_money' => [
            'amount' => (int) round($price * 100),
            'currency' => 'AUD',
        ],
    ];
}

if (!$lineItems) {
    http_response_code(400);
    exit('Your cart does not contain any valid products.');
}

$payload = [
    'idempotency_key' => bin2hex(random_bytes(16)),
    'order' => [
        'location_id' => $squareLocationId,
        'reference_id' => session_id(),
        'line_items' => $lineItems,
    ],
    'checkout_options' => [
        'redirect_url' => 'http://localhost/INTE1071-A2-Q1/billing.php?payment=square_success',
        'ask_for_shipping_address' => true,
    ],
];

$buyerAddress = [
    'first_name' => (string) ($billing['firstname'] ?? ''),
    'last_name' => (string) ($billing['lastname'] ?? ''),
    'address_line_1' => (string) ($billing['address'] ?? ''),
    'locality' => (string) ($billing['city'] ?? ''),
    'administrative_district_level_1' => (string) ($billing['state'] ?? ''),
    'postal_code' => (string) ($billing['zip'] ?? ''),
    'country' => (string) ($billing['country'] ?? ''),
];

if (!empty($billing['address2'])) {
    $buyerAddress['address_line_2'] = (string) $billing['address2'];
}

$payload['pre_populated_data'] = [];
if (!empty($billing['email']) && filter_var($billing['email'], FILTER_VALIDATE_EMAIL)) {
    $payload['pre_populated_data']['buyer_email'] = $billing['email'];
}
if ($buyerAddress['address_line_1'] !== '' && $buyerAddress['locality'] !== '') {
    $payload['pre_populated_data']['buyer_address'] = $buyerAddress;
}

$curl = curl_init('https://connect.squareupsandbox.com/v2/online-checkout/payment-links');
curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $squareAccessToken,
        'Content-Type: application/json',
        'Accept: application/json',
        'Square-Version: 2026-09-16',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$responseBody = curl_exec($curl);
$curlError = curl_error($curl);
$httpStatus = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
curl_close($curl);

if ($responseBody === false || $curlError) {
    http_response_code(502);
    exit('Unable to connect to Square Sandbox.');
}

$response = json_decode($responseBody, true);
$paymentLink = $response['payment_link']['url'] ?? $response['payment_link']['long_url'] ?? '';

if ($httpStatus < 200 || $httpStatus >= 300 || !$paymentLink) {
    http_response_code(502);
    $squareError = $response['errors'][0] ?? [];
    $errorCode = $squareError['code'] ?? 'UNKNOWN_ERROR';
    $errorDetail = $squareError['detail'] ?? 'No additional details were provided.';
    exit('Square could not create the checkout link (HTTP ' . $httpStatus . ', ' . htmlspecialchars($errorCode, ENT_QUOTES, 'UTF-8') . '): ' . htmlspecialchars($errorDetail, ENT_QUOTES, 'UTF-8'));
}

header('Location: ' . $paymentLink, true, 303);
exit;