<?php

require_once __DIR__ . '/mail.php';

/*
|--------------------------------------------------------------------------
| Order confirmation + admin notification emails
|--------------------------------------------------------------------------
| CHANGED: this used to call getMailer() directly, which meant it always
| went through SMTP. So even after the OTP mail was switched to the Brevo
| API, order emails kept failing on the live server. Both mails now go
| through sendAppMail(), which picks whichever transport actually works.
|
| Also removed the old `die("Customer email is empty")` on line 40. That
| killed the entire checkout request mid-way, so the customer saw a blank
| page and never reached receipt.php - even though their order HAD been
| saved. A missing email is now just logged and skipped.
|--------------------------------------------------------------------------
*/

function sendInvoiceMail($conn, $order)
{
    try {

        $orderId = (int) $order['order_id'];

        /* -------------------------
           Build the product rows
        -------------------------- */

        $productHtml = '';

        $productQuery = mysqli_query(
            $conn,
            "SELECT * FROM order_items WHERE order_id = $orderId"
        );

        if ($productQuery) {
            while ($item = mysqli_fetch_assoc($productQuery)) {
                $productHtml .= '
                <tr>
                    <td>' . htmlspecialchars($item['product_name']) . '</td>
                    <td>' . (int) $item['qty'] . '</td>
                    <td>&#8377;' . htmlspecialchars($item['price']) . '</td>
                </tr>';
            }
        }

        $settings = mailSettings($conn);

        /* -------------------------
           1. Customer confirmation
        -------------------------- */

        if (empty($order['email'])) {

            mailLog("sendInvoiceMail: order #$orderId has no customer email, "
                . "skipping customer copy.");

        } else {

            $customerBody = '
            <h1 style="color:#A67C52">Shreeji Decoration</h1>

            <hr>

            <p>Hello <b>' . htmlspecialchars($order['customer_name']) . '</b></p>

            <p>Thank you for your order.</p>

            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <tr>
                    <td>Order ID</td>
                    <td>' . htmlspecialchars($order['order_id']) . '</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>' . htmlspecialchars($order['phone']) . '</td>
                </tr>
                <tr>
                    <td>Payment</td>
                    <td>' . htmlspecialchars($order['payment_method']) . '</td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td>&#8377;' . htmlspecialchars($order['total_amount']) . '</td>
                </tr>
            </table>

            <br>

            <p>Your invoice can be viewed from My Orders.</p>';

            $customerError = null;

            $sent = sendAppMail(
                $conn,
                $order['email'],
                $order['customer_name'],
                'Order Confirmation - ' . $order['order_id'],
                $customerBody,
                '',
                $customerError
            );

            if (!$sent) {
                mailLog("sendInvoiceMail: customer copy for order #$orderId "
                    . "failed - $customerError");
            }
        }

        /* -------------------------
           2. Admin notification
        -------------------------- */

        $adminEmail = !empty($settings['email'])
            ? $settings['email']
            : (!empty($settings['smtp_email']) ? $settings['smtp_email'] : '');

        if (empty($adminEmail)) {

            mailLog("sendInvoiceMail: no admin email set in Admin > Settings, "
                . "skipping admin notification for order #$orderId.");

            return;
        }

        $adminBody = '
        <h2 style="color:#A67C52">New Order Received</h2>

        <hr>

        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <td><b>Order ID</b></td>
                <td>' . htmlspecialchars($order['order_id']) . '</td>
            </tr>
            <tr>
                <td><b>Customer Name</b></td>
                <td>' . htmlspecialchars($order['customer_name']) . '</td>
            </tr>
            <tr>
                <td><b>Phone</b></td>
                <td>' . htmlspecialchars($order['phone']) . '</td>
            </tr>
            <tr>
                <td><b>Email</b></td>
                <td>' . htmlspecialchars($order['email']) . '</td>
            </tr>
            <tr>
                <td><b>Payment Method</b></td>
                <td>' . htmlspecialchars($order['payment_method']) . '</td>
            </tr>
            <tr>
                <td><b>Total Amount</b></td>
                <td>&#8377;' . htmlspecialchars($order['total_amount']) . '</td>
            </tr>
            <tr>
                <td><b>Address</b></td>
                <td>' . htmlspecialchars($order['address']) . '</td>
            </tr>
        </table>

        <br>

        <h3>Products Ordered</h3>

        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
            ' . $productHtml . '
        </table>

        <br>

        <p>A new order has been placed on the website.</p>';

        $adminError = null;

        $adminSent = sendAppMail(
            $conn,
            $adminEmail,
            'Shreeji Decoration Admin',
            'New Order Received - #' . $order['order_id'],
            $adminBody,
            '',
            $adminError
        );

        if (!$adminSent) {
            mailLog("sendInvoiceMail: admin copy for order #$orderId "
                . "failed - $adminError");
        }

    } catch (\Throwable $e) {

        // The order is already saved by the time this runs, so a failed
        // invoice email must never take down the checkout.
        mailLog('sendInvoiceMail crashed: ' . $e->getMessage());
    }
}
