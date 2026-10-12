<?php

/*
|--------------------------------------------------------------------------
| STEP 2 of the online payment flow
|--------------------------------------------------------------------------
| Razorpay's popup calls our JS handler with three values. The browser
| posts them here. This script decides whether the order is really paid.
|
|   1. Verify the HMAC-SHA256 signature with the Key Secret.
|      This proves the callback genuinely came from Razorpay.
|   2. Independently ask Razorpay's API what that payment's status and
|      amount actually are, and check they match our order.
|   3. Only then: mark paid, write order_items, reduce stock, email the
|      invoice.
|
| Step 3 is wrapped so it can only ever run ONCE per order, because this
| endpoint and the webhook can both fire for the same payment.
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();

require_once '../config/db.php';
require_once '../includes/razorpay.php';
require_once '../includes/razorpay-finalize.php';

function jsonOut($arr, $code = 200)
{
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonOut(['success' => false, 'message' => 'Invalid request.'], 405);
    }

    $paymentId = trim($_POST['razorpay_payment_id'] ?? '');
    $rzpOrder  = trim($_POST['razorpay_order_id']   ?? '');
    $signature = trim($_POST['razorpay_signature']  ?? '');

    if ($paymentId === '' || $rzpOrder === '' || $signature === '') {
        jsonOut(['success' => false, 'message' => 'Incomplete payment response.'], 400);
    }

    $cfg = razorpayConfig($conn);

    if (!$cfg || $cfg['key_secret'] === '') {
        jsonOut(['success' => false, 'message' => 'Payment configuration missing.'], 500);
    }

    /* =========================================================
       CHECK 1 — signature
       ========================================================= */

    if (!razorpayVerifySignature($cfg, $rzpOrder, $paymentId, $signature)) {

        mailLog("razorpay-verify: SIGNATURE MISMATCH for $rzpOrder / $paymentId. "
            . "Possible tampering, or Key Secret does not match Key ID.");

        markOrderFailed($conn, $rzpOrder, 'signature mismatch');

        jsonOut([
            'success' => false,
            'message' => 'We could not verify this payment. If money was deducted '
                . 'it will be refunded automatically within 5-7 working days.',
        ], 400);
    }

    /* =========================================================
       Find our order
       ========================================================= */

    $escRzp = mysqli_real_escape_string($conn, $rzpOrder);

    $orderQ = mysqli_query(
        $conn,
        "SELECT * FROM orders WHERE razorpay_order_id='$escRzp' LIMIT 1"
    );

    $order = $orderQ ? mysqli_fetch_assoc($orderQ) : null;

    if (!$order) {
        mailLog("razorpay-verify: no local order found for $rzpOrder");
        jsonOut(['success' => false, 'message' => 'Order not found.'], 404);
    }

    // Already handled (e.g. the webhook beat us to it) — just succeed.
    if ($order['payment_status'] === 'paid') {
        jsonOut([
            'success'  => true,
            'order_id' => $order['order_id'],
            'message'  => 'Payment already confirmed.',
        ]);
    }

    /* =========================================================
       CHECK 2 — ask Razorpay directly
       A correct signature proves the message is authentic, but not that
       the amount matches what we asked for. Fetching the payment closes
       that gap.
       ========================================================= */

    $fetchError = null;

    $payment = razorpayFetchPayment($cfg, $paymentId, $fetchError);

    if (!$payment) {
        // Razorpay unreachable. Do not mark failed — the webhook will
        // settle it. Tell the customer it is being confirmed.
        mailLog("razorpay-verify: fetch failed for $paymentId: $fetchError");

        jsonOut([
            'success'  => true,
            'pending'  => true,
            'order_id' => $order['order_id'],
            'message'  => 'Payment received and being confirmed.',
        ]);
    }

    $expectedPaise = (int) round(((float) $order['total_amount']) * 100);
    $actualPaise   = (int) ($payment['amount'] ?? 0);
    $status        = $payment['status'] ?? '';

    if ($actualPaise !== $expectedPaise) {

        mailLog("razorpay-verify: AMOUNT MISMATCH order {$order['order_id']} "
            . "expected $expectedPaise paise, got $actualPaise paise.");

        markOrderFailed($conn, $rzpOrder, 'amount mismatch');

        jsonOut([
            'success' => false,
            'message' => 'The paid amount did not match your order total. '
                . 'Please contact us before re-ordering.',
        ], 400);
    }

    if ($status !== 'captured' && $status !== 'authorized') {

        markOrderFailed($conn, $rzpOrder, 'payment status: ' . $status);

        jsonOut([
            'success' => false,
            'message' => 'Your payment did not go through. Please try again.',
        ], 400);
    }

    /* =========================================================
       All checks passed — finalize
       ========================================================= */

    finalizePaidOrder($conn, $order, $paymentId, $signature);

    jsonOut([
        'success'  => true,
        'order_id' => $order['order_id'],
        'message'  => 'Payment successful.',
    ]);

} catch (\Throwable $e) {

    error_log('razorpay-verify.php failed: ' . $e->getMessage());

    jsonOut([
        'success' => false,
        'message' => 'A server error occurred while confirming your payment. '
            . 'Please check My Orders before paying again.',
    ], 500);
}
