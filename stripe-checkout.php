<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/secrets.php';

$environmentStripeSecretKey = getenv('STRIPE_SECRET_KEY');
$stripeSecretKey = $environmentStripeSecretKey ?: ($stripeSecretKey ?? '');
if (!$stripeSecretKey) {
  http_response_code(500);
  exit('Stripe is not configured. Set the STRIPE_SECRET_KEY environment variable.');
}

$cartItems = $_SESSION['cart'] ?? [];
if (!$cartItems) {
  header('Location: cart.php');
  exit;
}

$lineItems = [];
foreach ($cartItems as $item) {
  $price = (float) ($item['price'] ?? 0);
  $quantity = max(1, (int) ($item['qty'] ?? 1));

  if ($price <= 0 || empty($item['name'])) {
    continue;
  }

  $lineItems[] = [
    'price_data' => [
      'currency' => 'aud',
      'product_data' => [
        'name' => (string) $item['name'],
      ],
      'unit_amount' => (int) round($price * 100),
    ],
    'quantity' => $quantity,
  ];
}

if (!$lineItems) {
  http_response_code(400);
  exit('Your cart does not contain any valid products.');
}

$yourDomain = 'http://localhost/INTE1071-A2-Q1';
$billingEmail = trim((string) ($_SESSION['billing']['email'] ?? ''));
$stripe = new \Stripe\StripeClient($stripeSecretKey);

$sessionOptions = [
  'line_items' => $lineItems,
  'mode' => 'payment',
  'billing_address_collection' => 'required',
  'success_url' => $yourDomain . '/billing.php?payment=success&session_id={CHECKOUT_SESSION_ID}',
  'cancel_url' => $yourDomain . '/billing.php?payment=cancelled',
  'client_reference_id' => session_id(),
  'integration_identifier' => 'alice-bike-shop-checkout',
];

if ($billingEmail !== '' && filter_var($billingEmail, FILTER_VALIDATE_EMAIL)) {
  $sessionOptions['customer_email'] = $billingEmail;
}

try {
  $checkoutSession = $stripe->checkout->sessions->create($sessionOptions);
} catch (\Throwable $exception) {
  http_response_code(500);
  exit('Unable to start Stripe Checkout: ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
}

header('Location: ' . $checkoutSession->url, true, 303);
exit;
