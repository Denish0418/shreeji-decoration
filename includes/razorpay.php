<?php

/*
|--------------------------------------------------------------------------
| Razorpay helper
|--------------------------------------------------------------------------
| Talks to Razorpay over plain HTTPS (port 443) with cURL. No Composer
| package is needed — the official SDK is a thin wrapper over these same
| three REST calls, and keeping it dependency-free means nothing extra to
| install when you deploy to a shared host.
|
| Everything here runs SERVER-SIDE ONLY. The Key Secret must never appear
| in any HTML, JavaScript or AJAX response — it is the thing that proves a
| payment notification really came from Razorpay.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/mail.php';   // for mailLog()


/**
 * Read Razorpay credentials out of the settings table.
 */
function razorpayConfig($conn)
{
    $q = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");

    $s = $q ? mysqli_fetch_assoc($q) : null;

    if (!$s) {
        return null;
    }

    return [
        'enabled'        => !empty($s['razorpay_enabled']),
        'mode'           => $s['razorpay_mode'] ?? 'test',
        'key_id'         => trim($s['razorpay_key_id'] ?? ''),
        'key_secret'     => trim($s['razorpay_key_secret'] ?? ''),
        'webhook_secret' => trim($s['razorpay_webhook_secret'] ?? ''),
        'website_name'   => $s['website_name'] ?? 'Shreeji Decoration',
    ];
}


/**
 * Is online payment usable right now?
 */
function razorpayReady($conn, &$reason = null)
{
    $cfg = razorpayConfig($conn);

    if (!$cfg) {
        $reason = 'Settings table is empty.';
        return false;
    }

    if (!$cfg['enabled']) {
        $reason = 'Online payment is switched off in Admin > Settings.';
        return false;
    }

    if ($cfg['key_id'] === '' || $cfg['key_secret'] === '') {
        $reason = 'Razorpay Key ID or Key Secret is missing in Admin > Settings.';
        return false;
    }

    // Live keys start with rzp_live_, test keys with rzp_test_
    if (strpos($cfg['key_id'], 'rzp_') !== 0) {
        $reason = 'Razorpay Key ID looks wrong — it should start with rzp_test_ or rzp_live_.';
        return false;
    }

    return true;
}


/**
 * Low-level authenticated request to the Razorpay REST API.
 */
function razorpayRequest($cfg, $method, $path, $body = null, &$error = null)
{
    if (!function_exists('curl_init')) {
        $error = 'The PHP cURL extension is not installed. Ask your host to enable php-curl.';
        return null;
    }

    $url = 'https://api.razorpay.com/v1' . $path;

    $ch = curl_init($url);

    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => $cfg['key_id'] . ':' . $cfg['key_secret'],
        CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_CONNECTTIMEOUT => 10,
    ];

    if ($body !== null) {
        $options[CURLOPT_POSTFIELDS] = json_encode($body);
    }

    curl_setopt_array($ch, $options);

    $response  = curl_exec($ch);
    $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($curlError) {
        $error = 'Could not reach Razorpay: ' . $curlError;
        mailLog("Razorpay $method $path -> cURL error: $curlError");
        return null;
    }

    $decoded = json_decode((string) $response, true);

    mailLog("Razorpay $method $path -> HTTP $httpCode | "
        . substr((string) $response, 0, 400));

    if ($httpCode < 200 || $httpCode >= 300) {

        $apiMessage = $decoded['error']['description']
            ?? ('Razorpay returned HTTP ' . $httpCode);

        if ($httpCode === 401) {
            $apiMessage = 'Razorpay rejected your API keys (HTTP 401). '
                . 'Check that the Key ID and Key Secret are from the same '
                . 'mode — a Test Key ID with a Live Key Secret will fail here.';
        }

        $error = $apiMessage;
        return null;
    }

    return $decoded;
}


/**
 * Create a Razorpay Order.
 *
 * IMPORTANT: Razorpay works in PAISE, not rupees. Rs 1,234.50 is 123450.
 * Getting this wrong by a factor of 100 is the single most common mistake
 * in a first integration.
 *
 * @param float  $amountRupees  The amount calculated SERVER-SIDE.
 * @param string $receipt       Your own order id, for reconciliation.
 */
function razorpayCreateOrder($cfg, $amountRupees, $receipt, $notes = [], &$error = null)
{
    $paise = (int) round($amountRupees * 100);

    if ($paise < 100) {
        $error = 'Order total must be at least Rs 1 to pay online.';
        return null;
    }

    return razorpayRequest($cfg, 'POST', '/orders', [
        'amount'          => $paise,
        'currency'        => 'INR',
        'receipt'         => (string) $receipt,
        'payment_capture' => 1,   // auto-capture; no manual step in the dashboard
        'notes'           => $notes,
    ], $error);
}


/**
 * Verify the signature returned by Razorpay Checkout.
 *
 * This is the whole security model. Razorpay signs
 * "<razorpay_order_id>|<razorpay_payment_id>" with your Key Secret using
 * HMAC-SHA256. Only you and Razorpay know that secret, so a matching
 * signature proves the success callback is real and not somebody POSTing
 * made-up payment ids at your verify endpoint.
 *
 * NEVER mark an order paid without this check passing.
 */
function razorpayVerifySignature($cfg, $razorpayOrderId, $razorpayPaymentId, $signature)
{
    $expected = hash_hmac(
        'sha256',
        $razorpayOrderId . '|' . $razorpayPaymentId,
        $cfg['key_secret']
    );

    // hash_equals is timing-safe; == is not.
    return hash_equals($expected, (string) $signature);
}


/**
 * Verify a webhook payload signature (different header, same idea).
 */
function razorpayVerifyWebhook($cfg, $rawBody, $signature)
{
    if ($cfg['webhook_secret'] === '') {
        return false;
    }

    $expected = hash_hmac('sha256', $rawBody, $cfg['webhook_secret']);

    return hash_equals($expected, (string) $signature);
}


/**
 * Ask Razorpay what it thinks the payment's status and amount are.
 * Used as a second check so a tampered client cannot confirm an order
 * that was actually for a smaller amount.
 */
function razorpayFetchPayment($cfg, $paymentId, &$error = null)
{
    return razorpayRequest($cfg, 'GET', '/payments/' . urlencode($paymentId), null, $error);
}


/**
 * Refund a payment (full refund when $amountRupees is null).
 */
function razorpayRefund($cfg, $paymentId, $amountRupees = null, &$error = null)
{
    $body = [];

    if ($amountRupees !== null) {
        $body['amount'] = (int) round($amountRupees * 100);
    }

    return razorpayRequest(
        $cfg,
        'POST',
        '/payments/' . urlencode($paymentId) . '/refund',
        $body,
        $error
    );
}
