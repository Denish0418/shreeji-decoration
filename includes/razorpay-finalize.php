<?php

/*
|--------------------------------------------------------------------------
| Order finalization
|--------------------------------------------------------------------------
| Shared by api/razorpay-verify.php (browser callback) and
| api/razorpay-webhook.php (server-to-server callback).
|
| Both can fire for the same payment — that is by design, it is what makes
| the flow reliable when a customer closes the tab right after paying. So
| everything here MUST be safe to call twice. The UPDATE ... WHERE
| payment_status <> 'paid' plus affected_rows check is the lock: whichever
| call gets there first does the work, the second one sees 0 rows changed
| and returns immediately.
|
| Without that guard you get duplicate invoice emails and stock reduced
| twice for a single order.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/send-invoice.php';
require_once __DIR__ . '/mail.php';


/**
 * Mark an order paid and do all the post-payment work, exactly once.
 *
 * @return bool true if this call did the work, false if it was already done.
 */
function finalizePaidOrder($conn, $order, $paymentId, $signature = '')
{
    $orderId    = (int) $order['order_id'];
    $escPayment = mysqli_real_escape_string($conn, $paymentId);
    $escSig     = mysqli_real_escape_string($conn, $signature);

    /* ---------------------------------------------------------
       The idempotency lock.
       --------------------------------------------------------- */

    mysqli_query($conn, "
        UPDATE orders
           SET payment_status      = 'paid',
               status              = 'Confirmed',
               razorpay_payment_id = '$escPayment',
               razorpay_signature  = '$escSig',
               amount_paid         = total_amount,
               paid_at             = NOW()
         WHERE order_id = '$orderId'
           AND payment_status <> 'paid'
    ");

    if (mysqli_affected_rows($conn) === 0) {
        mailLog("finalizePaidOrder: order $orderId already finalized, skipping.");
        return false;
    }

    mailLog("finalizePaidOrder: order $orderId marked paid (payment $paymentId).");

    /* ---------------------------------------------------------
       Write order_items from the SERVER-SIDE priced snapshot that
       razorpay-create-order.php stored in orders.products.
       --------------------------------------------------------- */

    $items = json_decode($order['products'], true);

    if (is_array($items)) {

        foreach ($items as $item) {

            $productId   = (int) ($item['id'] ?? 0);
            $productName = mysqli_real_escape_string($conn, $item['name'] ?? '');
            $price       = (float) ($item['price'] ?? 0);
            $qty         = (int) ($item['qty'] ?? 0);
            $lineTotal   = round($price * $qty, 2);

            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            mysqli_query($conn, "
                INSERT INTO order_items
                    (order_id, product_id, product_name, price, qty, total)
                VALUES
                    ('$orderId', '$productId', '$productName',
                     '$price', '$qty', '$lineTotal')
            ");

            /* Reduce stock in one atomic statement. GREATEST(...) keeps
               it from going negative, and doing it in SQL avoids the
               read-then-write race the old checkout code had. */

            mysqli_query($conn, "
                UPDATE products
                   SET stock = GREATEST(stock - $qty, 0)
                 WHERE id = '$productId'
            ");
        }
    }

    /* ---------------------------------------------------------
       Invoice + admin notification.
       Re-read the order so the email shows the paid status.
       --------------------------------------------------------- */

    $fresh = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM orders WHERE order_id='$orderId' LIMIT 1")
    );

    if ($fresh) {
        sendInvoiceMail($conn, $fresh);
    }

    return true;
}


/**
 * Mark an online order as failed so it stops sitting in "Awaiting Payment".
 * Never touches an order that is already paid.
 */
function markOrderFailed($conn, $razorpayOrderId, $why = '')
{
    $esc = mysqli_real_escape_string($conn, $razorpayOrderId);

    mysqli_query($conn, "
        UPDATE orders
           SET payment_status = 'failed',
               status         = 'Payment Failed'
         WHERE razorpay_order_id = '$esc'
           AND payment_status <> 'paid'
    ");

    mailLog("markOrderFailed: $razorpayOrderId — $why");
}
