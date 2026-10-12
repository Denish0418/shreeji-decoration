<?php

require_once 'includes/session.php';
require_once 'config/db.php';
require_once 'includes/razorpay.php';

/*
|--------------------------------------------------------------------------
| Customer Login Protection
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['customer_logged_in']) ||
    $_SESSION['customer_logged_in'] !== true
) {

    header("Location: retail.php");

    exit;
}

/*
|--------------------------------------------------------------------------
| Is online payment usable? If Razorpay is off or unconfigured, the option
| is simply hidden and the site keeps working on Cash On Delivery alone.
|--------------------------------------------------------------------------
*/

$razorpayReason    = null;
$razorpayAvailable = razorpayReady($conn, $razorpayReason);

$razorpayCfg       = $razorpayAvailable ? razorpayConfig($conn) : null;
$razorpayTestMode  = $razorpayCfg && strpos($razorpayCfg['key_id'], 'rzp_test_') === 0;




if (isset($_POST['place_order'])) {

    $customer_id = (int)($_SESSION['customer_id'] ?? 0);

    $name = mysqli_real_escape_string(
        $conn,
        $_SESSION['customer_name']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_SESSION['customer_phone']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_SESSION['customer_email']
    );
    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {
        die("Invalid Email Address");
    }
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment = mysqli_real_escape_string($conn, $_POST['payment']);

    /*
    | This POST branch now only ever handles Cash On Delivery. Online
    | payments never reach here - they go through
    | api/razorpay-create-order.php and api/razorpay-verify.php, which
    | recalculate the total from the products table instead of trusting
    | $_POST['total'].
    |
    | Guard against someone posting payment=Razorpay directly to get an
    | order marked without paying.
    */

    if (stripos($payment, 'razorpay') !== false || stripos($payment, 'online') !== false) {
        header("Location: checkout.php?error=use_online_flow");
        exit;
    }

    $products = $_POST['products'];
    $cart = json_decode($_POST['cart_data'], true);
    $total = $_POST['total'];

    $customer_latitude =
        $_POST['customer_latitude'];

    $customer_longitude =
        $_POST['customer_longitude'];

    $distance_km =
        $_POST['distance_km'];

    $delivery_charge =
        $_POST['delivery_charge'];

    // 8 DIGIT ORDER ID
    $order_id = rand(10000000, 99999999);

    mysqli_query($conn, "
    INSERT INTO orders(
    customer_id,
    order_id,
        customer_name,
        phone,
        email,
        address,
        payment_method,
        products,
        customer_latitude,
customer_longitude,

distance_km,

delivery_charge,
        total_amount
    ) VALUES(
    '$customer_id',
    '$order_id',
        '$name',
        '$phone',
        '$email',
        '$address',
        '$payment',
        '$products',
        '$customer_latitude',
'$customer_longitude',

'$distance_km',

'$delivery_charge',
        '$total'
    )
    ");
    require_once
        'includes/send-invoice.php';

    $orderData =
        mysqli_fetch_assoc(
            mysqli_query(
                $conn,
                "SELECT *
FROM orders
WHERE order_id='$order_id'
LIMIT 1"
            )
        );


    /* ===========================
   SAVE PRODUCTS INTO order_items
=========================== */

    if ($cart && is_array($cart)) {

        foreach ($cart as $item) {

            $product_id = (int)$item['id'];

            $product_name = mysqli_real_escape_string(
                $conn,
                $item['name']
            );

            $price = (float)$item['price'];

            $qty = (int)$item['qty'];

            $total_item = $price * $qty;

            $result = mysqli_query($conn, "
            INSERT INTO order_items
            (
                order_id,
                product_id,
                product_name,
                price,
                qty,
                total
            )
            VALUES
            (
                '$order_id',
                '$product_id',
                '$product_name',
                '$price',
                '$qty',
                '$total_item'
            )
        ");
            if (!$result) {
                die("Order Error : " . mysqli_error($conn));
            }
        }
    }
    // UPDATE PRODUCT STOCK

    if ($cart && is_array($cart)) {

        foreach ($cart as $item) {

            $product_id = (int)$item['id'];

            $qty = (int)$item['qty'];

            // GET CURRENT STOCK

            $stock_q = mysqli_query(
                $conn,

                "SELECT stock FROM products
        WHERE id='$product_id'"
            );

            $stock_data = mysqli_fetch_assoc($stock_q);

            $current_stock = (int)$stock_data['stock'];

            // CALCULATE NEW STOCK

            $new_stock = $current_stock - $qty;

            if ($new_stock < 0) {
                $new_stock = 0;
            }

            // UPDATE STOCK

            mysqli_query(
                $conn,

                "UPDATE products

        SET stock='$new_stock'

        WHERE id='$product_id'"
            );
        }
    }
    sendInvoiceMail(
        $conn,
        $orderData
    );
    header("Location: receipt.php?order_id=$order_id");

    exit();
}

/*
|--------------------------------------------------------------------------
| Profile Address
|--------------------------------------------------------------------------
| Checkout now shows the customer's saved profile address (set on
| customer/profile.php) instead of a list of past order addresses.
*/

$lastAddress = '';

$profileAddressQuery = mysqli_query(
    $conn,
    "SELECT address FROM customers
     WHERE id = " . (int) ($_SESSION['customer_id'] ?? 0) . "
     LIMIT 1"
);

if ($profileAddressQuery && mysqli_num_rows($profileAddressQuery) > 0) {
    $lastAddress = mysqli_fetch_assoc($profileAddressQuery)['address'] ?? '';
    $lastAddress = trim($lastAddress);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #FDF8F0;
            font-family: Poppins;
        }

        .checkout-box {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .08);
        }

        .place-btn {
            background: linear-gradient(135deg, #C9A063, #B8863F);
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1.5px solid #eadfd2;
            color: #2B1600;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 700;
            padding: 10px 20px 10px 16px;
            border-radius: 50px;
            margin-bottom: 18px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .05);
            transition: all .25s ease;
        }

        .back-btn:hover {
            background: linear-gradient(135deg, #C9A063, #B8863F);
            border-color: transparent;
            color: #fff;
            transform: translateX(-3px);
            box-shadow: 0 8px 22px rgba(232, 119, 46, .3);
        }

        .back-btn i {
            font-size: 1rem;
            transition: transform .25s ease;
        }

        .back-btn:hover i {
            transform: translateX(-3px);
        }

        .address-option {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border: 1.5px solid #eee;
            border-radius: 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all .2s ease;
            position: relative;
        }

        .address-option:hover {
            border-color: #E8A96A;
            background: #FFFAF3;
        }

        .address-option i:first-child {
            color: #C9A063;
            font-size: 1rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .address-option p {
            font-size: .92rem;
            color: #333;
            flex: 1;
        }

        .address-option .check-icon {
            display: none;
            color: #B8863F;
            font-size: 1.1rem;
        }

        .address-option.selected {
            border-color: #B8863F;
            background: #FFF6EC;
            box-shadow: 0 2px 10px rgba(232, 119, 46, .1);
        }

        .address-option.selected .check-icon {
            display: block;
        }

        #newAddressOption {
            color: #7A5A2E;
            font-weight: 600;
        }

        #newAddressOption i {
            color: #C9A063;
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <a href="cart.php" id="checkoutBackBtn" class="back-btn">
                    <i class="bi bi-arrow-left"></i> Back
                </a>

                <div class="checkout-box">

                    <h2 class="fw-bold mb-4">
                        Checkout
                    </h2>

                    <form method="POST" id="checkoutForm">

                        <input type="hidden" name="place_order" value="1">

                        <input type="hidden"
                            name="cart_data"
                            id="cart_data">

                        <div class="mb-4">

                            <h5 class="fw-bold mb-3">

                                Customer Information

                            </h5>

                            <div class="card border-0 shadow-sm">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-md-4">

                                            <label class="text-muted">

                                                Full Name

                                            </label>

                                            <p class="fw-bold mb-0">

                                                <?= htmlspecialchars($_SESSION['customer_name'] ?? ''); ?>

                                            </p>

                                        </div>

                                        <div class="col-md-4">

                                            <label class="text-muted">

                                                Phone Number

                                            </label>

                                            <p class="fw-bold mb-0">

                                                <?= htmlspecialchars($_SESSION['customer_phone'] ?? ''); ?>

                                            </p>

                                        </div>

                                        <div class="col-md-4">

                                            <label class="text-muted">

                                                Email Address

                                            </label>

                                            <p class="fw-bold mb-0">

                                                <?= htmlspecialchars($_SESSION['customer_email'] ?? ''); ?>

                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="mb-2">
                                Address
                            </label>

                            <?php if ($lastAddress !== '') { ?>

                                <div id="savedAddressBox" class="address-option selected" onclick="showAddressEditor()">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($lastAddress)); ?></p>
                                    <span style="font-size:.78rem;color:#B8863F;font-weight:700;white-space:nowrap;">
                                        <i class="bi bi-pencil-fill me-1"></i>Change
                                    </span>
                                </div>

                                <textarea
                                    name="address"
                                    id="addressTextarea"
                                    class="form-control"
                                    rows="4"
                                    style="display:none;"
                                    required><?= htmlspecialchars($lastAddress); ?></textarea>

                                <p class="form-text mb-0">
                                    This is the address saved on your <a href="../customer/profile.php">profile</a>.
                                </p>

                            <?php } else { ?>

                                <textarea
                                    name="address"
                                    id="addressTextarea"
                                    class="form-control"
                                    rows="4"
                                    required></textarea>

                                <p class="form-text mb-0">
                                    Tip: save an address on your <a href="../customer/profile.php">profile</a> so it's ready here next time.
                                </p>

                            <?php } ?>

                        </div>

                        <div class="mb-4">

                            <label class="mb-2">
                                Payment Method
                            </label>

                            <div class="payment-options" id="paymentOptions">

                                <?php if ($razorpayAvailable) { ?>
                                    <label class="payment-option" for="pmOnline">
                                        <input type="radio"
                                            name="payment"
                                            id="pmOnline"
                                            value="Razorpay"
                                            checked>
                                        <span class="payment-option-icon">
                                            <i class="bi bi-credit-card-2-front"></i>
                                        </span>
                                        <span class="payment-option-text">
                                            <span class="payment-option-title">Pay Online</span>
                                            <span class="payment-option-sub">UPI, Card, Netbanking, Wallet</span>
                                        </span>
                                        <span class="payment-option-check">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </span>
                                    </label>
                                <?php } ?>

                                <label class="payment-option" for="pmCod">
                                    <input type="radio"
                                        name="payment"
                                        id="pmCod"
                                        value="Cash On Delivery"
                                        <?= $razorpayAvailable ? '' : 'checked' ?>>
                                    <span class="payment-option-icon">
                                        <i class="bi bi-cash-coin"></i>
                                    </span>
                                    <span class="payment-option-text">
                                        <span class="payment-option-title">Cash On Delivery</span>
                                        <span class="payment-option-sub">Pay when your order arrives</span>
                                    </span>
                                    <span class="payment-option-check">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                </label>

                            </div>

                            <style>
                                .payment-options {
                                    display: flex;
                                    flex-direction: column;
                                    gap: 12px;
                                }

                                .payment-option {
                                    position: relative;
                                    display: flex;
                                    align-items: center;
                                    gap: 14px;
                                    padding: 14px 16px;
                                    border: 2px solid #e5ddd2;
                                    border-radius: 12px;
                                    cursor: pointer;
                                    background: #fff;
                                    transition: border-color .15s ease, background .15s ease;
                                }

                                .payment-option:hover {
                                    border-color: #cbb99a;
                                }

                                .payment-option input[type="radio"] {
                                    position: absolute;
                                    opacity: 0;
                                    width: 0;
                                    height: 0;
                                }

                                .payment-option-icon {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 42px;
                                    height: 42px;
                                    border-radius: 50%;
                                    background: #FFF3E0;
                                    color: #8B4513;
                                    font-size: 1.2rem;
                                    flex-shrink: 0;
                                }

                                .payment-option-text {
                                    display: flex;
                                    flex-direction: column;
                                    flex-grow: 1;
                                }

                                .payment-option-title {
                                    font-weight: 600;
                                    color: #2b2b2b;
                                }

                                .payment-option-sub {
                                    font-size: 0.85rem;
                                    color: #888;
                                }

                                .payment-option-check {
                                    color: #d8d0c2;
                                    font-size: 1.3rem;
                                    flex-shrink: 0;
                                }

                                .payment-option:has(input:checked),
                                .payment-option.is-checked {
                                    border-color: #8B4513;
                                    background: #FFF8F0;
                                }

                                .payment-option:has(input:checked) .payment-option-check,
                                .payment-option.is-checked .payment-option-check {
                                    color: #8B4513;
                                }
                            </style>

                            <?php if ($razorpayAvailable && $razorpayTestMode) { ?>
                                <div class="alert alert-warning mt-2 mb-0 py-2 small">
                                    <b>Test mode.</b> No real money will be charged.
                                    Use card 4111 1111 1111 1111 with any future expiry and any CVV.
                                </div>
                            <?php } ?>

                        </div>
                        <input type="hidden"
                            name="products"
                            id="productsInput">

                        <input type="hidden"
                            name="total"
                            id="totalInput">
                        <input type="hidden"
                            name="customer_latitude"
                            id="customer_latitude">

                        <input type="hidden"
                            name="customer_longitude"
                            id="customer_longitude">

                        <input type="hidden"
                            name="distance_km"
                            id="distance_km">

                        <input type="hidden"
                            name="delivery_charge"
                            id="delivery_charge">

                        <button
                            type="button"
                            id="useLocationBtn"
                            class="btn btn-info mb-3"
                            onclick="getLocation()">

                            Use My Location

                        </button>

                        <div id="locationStatus"></div>

                        <div id="paymentError" class="alert alert-danger" style="display:none;"></div>

                        <button
                            type="button"
                            id="placeOrderBtn"
                            class="place-btn">

                            <span id="placeOrderBtnText">Place Order</span>
                            <span id="placeOrderBtnLoader" style="display:none;">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Processing...
                            </span>

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>
    <script>
        const customerId =
            <?= isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0 ?>;

        const CART_KEY = "CART_KEY" + customerId;

        document.getElementById('checkoutBackBtn').addEventListener('click', function(e) {
            let cameFromSameSite =
                document.referrer && document.referrer.indexOf(window.location.origin) === 0;

            if (cameFromSameSite && window.history.length > 1) {
                e.preventDefault();
                window.history.back();
            }
            // otherwise, let the normal href="cart.php" take over
        });

        function showAddressEditor() {
            let savedBox = document.getElementById('savedAddressBox');
            let textarea = document.getElementById('addressTextarea');

            if (savedBox) {
                savedBox.style.display = 'none';
            }

            textarea.style.display = 'block';
            textarea.focus();
        }

        let cart = JSON.parse(
            localStorage.getItem(CART_KEY)
        ) || [];

        if (cart.length === 0) {

            alert(
                "Your cart is empty"
            );

            window.location =
                "retail.php";

        }

        document.getElementById('productsInput').value =
            JSON.stringify(cart);

        let subtotal = 0;
        let gst = 0;

        cart.forEach(item => {

            subtotal += item.price * item.qty;

            gst +=
                (item.price * item.qty) *
                (item.gst_rate / 100);

        });

        let delivery =
            Number(
                localStorage.getItem('deliveryCharge')
            ) || 0;

        let total =
            subtotal +
            gst +
            delivery;

        document.getElementById('totalInput').value =
            total;
        document.getElementById('cart_data').value =
            localStorage.getItem(CART_KEY);

        document.getElementById(
                'distance_km'
            ).value =
            localStorage.getItem(
                'distanceKM'
            ) || 0;

        document.getElementById(
                'delivery_charge'
            ).value =
            localStorage.getItem(
                'deliveryCharge'
            ) || 0;

        function getLocation() {

            navigator.geolocation.getCurrentPosition(

                function(position) {

                    document.getElementById('customer_latitude').value =
                        position.coords.latitude;

                    document.getElementById('customer_longitude').value =
                        position.coords.longitude;

                    document.getElementById('locationStatus').innerHTML =
                        "Location Captured Successfully";

                    let useLocationBtn = document.getElementById('useLocationBtn');
                    if (useLocationBtn) {
                        useLocationBtn.style.display = 'none';
                    }

                },

                function() {

                    alert("Location Access Required");

                }

            );

        }
        window.onload = function() {

            getLocation();

        }

        /* ==========================================================
           PAYMENT HANDLING

           Two paths:

             Cash On Delivery -> submit the form exactly as before.

             Pay Online       -> never submit the form. Instead:
                                   1. POST the cart to
                                      api/razorpay-create-order.php
                                   2. Server recalculates the real total
                                      from the database and creates a
                                      Razorpay Order
                                   3. Open the Razorpay popup
                                   4. On success, POST the signature to
                                      api/razorpay-verify.php
                                   5. Server verifies, then we go to the
                                      receipt

           The browser never decides how much is charged. It only says
           which products and how many.
        ========================================================== */

        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const btnText = document.getElementById('placeOrderBtnText');
        const btnLoader = document.getElementById('placeOrderBtnLoader');
        const paymentError = document.getElementById('paymentError');

        function setBusy(busy) {
            placeOrderBtn.disabled = busy;
            btnText.style.display = busy ? 'none' : 'inline';
            btnLoader.style.display = busy ? 'inline' : 'none';
        }

        function showError(msg) {
            paymentError.textContent = msg;
            paymentError.style.display = 'block';
            paymentError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        function clearError() {
            paymentError.style.display = 'none';
        }

        function getAddress() {
            let ta = document.querySelector('textarea[name="address"]');
            return ta ? ta.value.trim() : '';
        }

        function refreshPaymentCardStyles() {
            document.querySelectorAll('.payment-option').forEach(function(card) {
                let radio = card.querySelector('input[type="radio"]');
                card.classList.toggle('is-checked', radio && radio.checked);
            });
        }

        document.querySelectorAll('input[name="payment"]').forEach(function(radio) {
            radio.addEventListener('change', refreshPaymentCardStyles);
        });

        refreshPaymentCardStyles();

        placeOrderBtn.addEventListener('click', function() {

            clearError();

            let address = getAddress();

            if (address === '') {
                showError('Please enter your delivery address.');
                return;
            }

            let checkedRadio = document.querySelector('input[name="payment"]:checked');
            let method = checkedRadio ? checkedRadio.value : 'Cash On Delivery';

            if (method !== 'Razorpay') {
                setBusy(true);
                document.getElementById('checkoutForm').submit();
                return;
            }

            startOnlinePayment(address);
        });


        function startOnlinePayment(address) {

            setBusy(true);

            let payload = new FormData();
            payload.append('cart_data', localStorage.getItem(CART_KEY) || '[]');
            payload.append('address', address);
            payload.append('distance_km', localStorage.getItem('distanceKM') || 0);
            payload.append('customer_latitude',
                document.getElementById('customer_latitude').value || '');
            payload.append('customer_longitude',
                document.getElementById('customer_longitude').value || '');

            fetch('api/razorpay-create-order.php', {
                    method: 'POST',
                    body: payload
                })
                .then(r => r.json())
                .then(data => {

                    if (!data.success) {
                        setBusy(false);
                        showError(data.message || 'Could not start the payment.');
                        return;
                    }

                    openRazorpay(data);
                })
                .catch(() => {
                    setBusy(false);
                    showError('Network problem. Please check your connection and try again.');
                });
        }


        function openRazorpay(data) {

            let options = {

                key: data.key_id, // public key, safe in the browser
                amount: data.amount, // paise, straight from Razorpay
                currency: data.currency,
                name: data.name,
                description: 'Order #' + data.order_id,
                order_id: data.rzp_order_id,

                prefill: {
                    name: data.prefill.name,
                    email: data.prefill.email,
                    contact: data.prefill.contact
                },

                theme: {
                    color: '#8B4513'
                },

                // Fires after a successful payment
                handler: function(response) {
                    verifyPayment(response, data.order_id);
                },

                modal: {
                    // Fires if the customer closes the popup without paying
                    ondismiss: function() {
                        setBusy(false);
                        showError('Payment cancelled. Your order has not been placed. ' +
                            'You can try again or choose Cash On Delivery.');
                    }
                }
            };

            let rzp = new Razorpay(options);

            rzp.on('payment.failed', function(response) {
                setBusy(false);
                showError('Payment failed: ' +
                    (response.error && response.error.description ?
                        response.error.description :
                        'please try another method.'));
            });

            rzp.open();
        }


        function verifyPayment(response, shopOrderId) {

            btnText.textContent = 'Confirming payment...';

            let payload = new FormData();
            payload.append('razorpay_payment_id', response.razorpay_payment_id);
            payload.append('razorpay_order_id', response.razorpay_order_id);
            payload.append('razorpay_signature', response.razorpay_signature);

            fetch('api/razorpay-verify.php', {
                    method: 'POST',
                    body: payload
                })
                .then(r => r.json())
                .then(result => {

                    if (result.success) {
                        // Order is confirmed server-side, so the cart can go.
                        localStorage.removeItem(CART_KEY);
                        localStorage.removeItem('deliveryCharge');
                        window.location = 'receipt.php?order_id=' + result.order_id;
                        return;
                    }

                    setBusy(false);
                    showError(result.message || 'We could not confirm your payment.');
                })
                .catch(() => {
                    /* The money may well have been taken. Never tell the
                       customer to pay again - send them to My Orders,
                       and let the webhook settle the order server-side. */
                    setBusy(false);
                    showError('Your payment went through but we could not confirm it here. ' +
                        'Please check My Orders in a minute before paying again. ' +
                        'Order #' + shopOrderId);
                });
        }
    </script>

    <?php if ($razorpayAvailable) { ?>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <?php } ?>
</body>

</html>