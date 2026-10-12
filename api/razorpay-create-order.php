<?php

/*
|--------------------------------------------------------------------------
| STEP 1 of the online payment flow
|--------------------------------------------------------------------------
| The browser posts the cart here. This script:
|
|   1. Recalculates the total from the products table (never trusts the
|      browser's total).
|   2. Saves a real order row with payment_status = 'created'.
|   3. Asks Razorpay to create a matching Order.
|   4. Returns the Razorpay order id + public Key ID so the browser can
|      open the payment popup.
|
| Stock is NOT reduced here and no invoice is sent — that only happens
| after the money is confirmed in razorpay-verify.php. Otherwise every
| abandoned payment would eat your inventory.
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();

function jsonFail($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

try {

    require_once '../config/db.php';
    require_once '../includes/cart-pricing.php';
    require_once '../includes/razorpay.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonFail('Invalid request.', 405);
    }

    /* -------------------------
       Must be a logged-in customer
    -------------------------- */

    if (empty($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
        jsonFail('Please log in again before paying.', 401);
    }

    $customerId = (int) ($_SESSION['customer_id'] ?? 0);

    $reason = null;

    if (!razorpayReady($conn, $reason)) {
        jsonFail('Online payment is not available right now. ' . $reason);
    }

    $cfg = razorpayConfig($conn);

    /* -------------------------
       Price the cart from the DB
    -------------------------- */

    $rawCart = json_decode($_POST['cart_data'] ?? '[]', true);

    $distanceKm = (float) ($_POST['distance_km'] ?? 0);

    $priced = priceCartServerSide($conn, $rawCart, $distanceKm);

    if (!$priced['ok']) {
        jsonFail($priced['error']);
    }

    /* -------------------------
       Customer + address
    -------------------------- */

    $name    = $_SESSION['customer_name']  ?? '';
    $phone   = $_SESSION['customer_phone'] ?? '';
    $email   = $_SESSION['customer_email'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if ($address === '') {
        jsonFail('Please enter your delivery address.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonFail('Your account has an invalid email address. Please update your profile.');
    }

    $lat = (float) ($_POST['customer_latitude'] ?? 0);
    $lng = (float) ($_POST['customer_longitude'] ?? 0);

    /* -------------------------
       Create our order row first, so we always have a record even if
       Razorpay times out halfway through.
    -------------------------- */

    $orderId = random_int(10000000, 99999999);

    $esc = function ($v) use ($conn) {
        return mysqli_real_escape_string($conn, (string) $v);
    };

    $productsJson = json_encode($priced['items']);

    $insert = mysqli_query($conn, "
        INSERT INTO orders (
            customer_id, order_id, customer_name, phone, email, address,
            payment_method, payment_status, products,
            customer_latitude, customer_longitude, distance_km,
            delivery_charge, total_amount, status
        ) VALUES (
            '$customerId',
            '$orderId',
            '" . $esc($name) . "',
            '" . $esc($phone) . "',
            '" . $esc($email) . "',
            '" . $esc($address) . "',
            'Razorpay',
            'created',
            '" . $esc($productsJson) . "',
            '" . $esc($lat) . "',
            '" . $esc($lng) . "',
            '" . (float) $distanceKm . "',
            '" . (float) $priced['delivery'] . "',
            '" . (float) $priced['total'] . "',
            'Awaiting Payment'
        )
    ");

    if (!$insert) {
        mailLog('razorpay-create-order: DB insert failed: ' . mysqli_error($conn));
        jsonFail('Could not start your order. Please try again.', 500);
    }

    /* -------------------------
       Ask Razorpay for an Order
    -------------------------- */

    $rzpError = null;

    $rzpOrder = razorpayCreateOrder(
        $cfg,
        $priced['total'],
        $orderId,
        [
            'shop_order_id' => (string) $orderId,
            'customer_name' => $name,
            'customer_phone' => $phone,
        ],
        $rzpError
    );

    if (!$rzpOrder || empty($rzpOrder['id'])) {

        mysqli_query($conn, "UPDATE orders
                                SET payment_status='failed', status='Payment Failed'
                              WHERE order_id='$orderId'");

        jsonFail($rzpError ?: 'Could not create the payment. Please try again.', 502);
    }

    $rzpOrderId = $esc($rzpOrder['id']);

    mysqli_query($conn, "UPDATE orders
                            SET razorpay_order_id='$rzpOrderId'
                          WHERE order_id='$orderId'");

    /* -------------------------
       Hand the browser what it needs.
       Note: key_id is PUBLIC and safe to send. key_secret is NOT and
       never appears in this response.
    -------------------------- */

    echo json_encode([
        'success'  => true,
        'key_id'   => $cfg['key_id'],
        'rzp_order_id' => $rzpOrder['id'],
        'amount'   => $rzpOrder['amount'],      // paise, straight from Razorpay
        'currency' => $rzpOrder['currency'],
        'order_id' => $orderId,
        'name'     => $cfg['website_name'],
        'prefill'  => [
            'name'    => $name,
            'email'   => $email,
            'contact' => $phone,
        ],
        'breakdown' => [
            'subtotal' => $priced['subtotal'],
            'gst'      => $priced['gst'],
            'delivery' => $priced['delivery'],
            'total'    => $priced['total'],
        ],
    ]);

} catch (\Throwable $e) {

    error_log('razorpay-create-order.php failed: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'A server error occurred. Please try again in a moment.',
    ]);
}
