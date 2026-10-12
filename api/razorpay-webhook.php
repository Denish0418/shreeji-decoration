<?php

/*
|--------------------------------------------------------------------------
| Razorpay Webhook
|--------------------------------------------------------------------------
| WHY YOU NEED THIS
|
| The browser callback in razorpay-verify.php runs in the customer's tab.
| If they pay and then immediately close the tab, lose signal, or their
| phone locks during the UPI app switch, that callback never fires — the
| money is taken but the order sits at "Awaiting Payment" forever, and you
| find out when they call to complain.
|
| Razorpay also notifies your SERVER directly. That message does not care
| what the customer's browser did. Configure it at:
|
|   Razorpay Dashboard > Settings > Webhooks > Add New Webhook
|   URL:    https://yourdomain.com/api/razorpay-webhook.php
|   Events: payment.captured, payment.failed
|   Secret: any random string — paste the SAME string into
|           Admin > Settings > Razorpay Webhook Secret
|
| This endpoint must be publicly reachable — do NOT put it behind a login.
| The HMAC signature check below is what keeps it safe.
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once '../config/db.php';
require_once '../includes/razorpay.php';
require_once '../includes/razorpay-finalize.php';

try {

    $rawBody = file_get_contents('php://input');

    $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

    $cfg = razorpayConfig($conn);

    if (!$cfg || $cfg['webhook_secret'] === '') {
        mailLog('razorpay-webhook: no webhook secret configured, ignoring.');
        http_response_code(200);   // 200 so Razorpay stops retrying
        exit;
    }

    /* ---------------------------------------------------------
       Anyone can POST to this URL. The signature is the only
       thing that proves the message came from Razorpay.
       --------------------------------------------------------- */

    if (!razorpayVerifyWebhook($cfg, $rawBody, $signature)) {
        mailLog('razorpay-webhook: signature check FAILED, rejecting.');
        http_response_code(401);
        exit;
    }

    $event = json_decode($rawBody, true);

    $type = $event['event'] ?? '';

    $payment = $event['payload']['payment']['entity'] ?? null;

    if (!$payment) {
        http_response_code(200);
        exit;
    }

    $paymentId  = $payment['id'] ?? '';
    $rzpOrderId = $payment['order_id'] ?? '';

    mailLog("razorpay-webhook: $type for order $rzpOrderId payment $paymentId");

    $esc = mysqli_real_escape_string($conn, $rzpOrderId);

    $order = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM orders WHERE razorpay_order_id='$esc' LIMIT 1")
    );

    if (!$order) {
        mailLog("razorpay-webhook: no local order for $rzpOrderId");
        http_response_code(200);
        exit;
    }

    if ($type === 'payment.captured') {

        // Same amount check as the browser path.
        $expectedPaise = (int) round(((float) $order['total_amount']) * 100);

        if ((int) ($payment['amount'] ?? 0) !== $expectedPaise) {
            mailLog("razorpay-webhook: AMOUNT MISMATCH on order {$order['order_id']}");
            markOrderFailed($conn, $rzpOrderId, 'webhook amount mismatch');
            http_response_code(200);
            exit;
        }

        // Safe to call even if razorpay-verify.php already did this.
        finalizePaidOrder($conn, $order, $paymentId, '');

    } elseif ($type === 'payment.failed') {

        markOrderFailed($conn, $rzpOrderId, 'webhook: payment.failed');
    }

    /* Always answer 200 once handled, otherwise Razorpay keeps retrying
       the same event for 24 hours. */

    http_response_code(200);

    echo 'ok';

} catch (\Throwable $e) {

    error_log('razorpay-webhook.php failed: ' . $e->getMessage());

    // 500 tells Razorpay to retry later, which is what we want if our
    // own database was briefly down.
    http_response_code(500);
}
