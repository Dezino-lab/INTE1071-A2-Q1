<?php
//global variable configuration
/*
* PayPal configuration
*/
// PayPal configuration
define('PAYPAL_ID', 'sb-pfulv52580591@business.example.com'); //seller email
define('PAYPAL_SANDBOX', TRUE); //TRUE or FALSE
// Redirect pages for this project.
define('PAYPAL_RETURN_URL', 'http://localhost/INTE1071-A2-Q1/billing.php?payment=success');
define('PAYPAL_CANCEL_URL', 'http://localhost/INTE1071-A2-Q1/billing.php?payment=cancelled');
define('PAYPAL_NOTIFY_URL', 'http://localhost/INTE1071-A2-Q1/ipn.php');
//define currency
define('PAYPAL_CURRENCY', 'AUD');

// Change not required
define('PAYPAL_URL', (PAYPAL_SANDBOX == true)? "https://www.sandbox.paypal.com/cgi-bin/webscr": "https://www.paypal.com/cgi-bin/webscr");
?>