<?php
require_once 'includes/session.php';

require_once 'config/db.php';

$settingsQuery =
    mysqli_query(
        $conn,
        "SELECT * FROM settings LIMIT 1"
    );

$settings =
    mysqli_fetch_assoc(
        $settingsQuery
    );
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Shreeji Decoration</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #FDF8F0;
            color: #2A1800;
        }

        .main-nav {
            background: #0D0800;
            padding: 14px 0;
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
        }

        .brand-tagline {
            font-size: .7rem;
            color: #C9A063;
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #C9A063, #B8863F);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #0D0800;
        }

        .cart-page {
            padding: 60px 0;
        }

        .cart-card {
            background: #fff;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .06);
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid #eee;
            gap: 20px;
        }

        .item-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: #FFF1D6;
        }

        .item-name {
            font-weight: 700;
            font-size: 1rem;
        }

        .item-price {
            color: #B8863F;
            font-weight: 700;
            margin-top: 4px;
        }

        .qty-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background: #2A1800;
            color: #fff;
            font-weight: 700;
        }

        .remove-btn {
            background: #ffefef;
            color: #E53935;
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: .8rem;
            font-weight: 600;
        }

        .summary-card {
            background: #fff;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .06);
            position: sticky;
            top: 100px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .total-row {
            font-size: 1.2rem;
            font-weight: 700;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, #C9A063, #B8863F);
            color: #0D0800;
            border: none;
            padding: 14px;
            border-radius: 14px;
            font-weight: 700;
            margin-top: 20px;
        }

        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>

<body>

    <nav class="main-nav">
        <div class="container d-flex align-items-center justify-content-between">

            <a href="index.php"
                class="d-flex align-items-center gap-2 text-decoration-none">

                <div class="brand-icon">
                    <i class="bi bi-flower1"></i>
                </div>

                <div>
                    <div class="brand-name">Shreeji Decoration</div>
                    <div class="brand-tagline">सजाएँ हर पल, हर जश्न</div>
                </div>

            </a>

            <a href="retail.php" class="btn btn-warning rounded-pill px-4 fw-bold">
                Continue Shopping
            </a>

        </div>
    </nav>

    <section class="cart-page">
        <div class="container">

            <div class="row g-4">

                <div class="col-lg-8">

                    <div class="cart-card">

                        <h3 class="mb-4 fw-bold">Shopping Cart</h3>

                        <div id="cartContainer"></div>

                    </div>

                </div>

                <div class="col-lg-4">

                    <div class="summary-card">

                        <h4 class="fw-bold mb-4">Order Summary</h4>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotal">₹0</span>
                        </div>

                        <div class="summary-row">
                            <span>GST</span>
                            <span id="gst">₹0</span>
                        </div>

                        <div class="summary-row">
                            <span>Delivery</span>
                            <span id="delivery">₹99</span>
                        </div>

                        <div class="summary-row total-row">
                            <span>Total</span>
                            <span id="grandTotal">₹0</span>
                        </div>

                        <a href="checkout.php"
                            class="checkout-btn d-flex align-items-center justify-content-center text-decoration-none">

                            Proceed to Checkout

                        </a>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <script>
        const customerId =
            <?= isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0 ?>;

        const CART_KEY = "CART_KEY" + customerId;

        const SHOP_LAT =
            <?php echo $settings['shop_latitude']; ?>;

        const SHOP_LNG =
            <?php echo $settings['shop_longitude']; ?>;

        const FREE_KM =
            <?php echo $settings['free_delivery_km']; ?>;

        const FREE_CHARGE =
            <?php echo $settings['free_delivery_charge']; ?>;

        const MEDIUM_KM =
            <?php echo $settings['medium_delivery_km']; ?>;

        const MEDIUM_CHARGE =
            <?php echo $settings['medium_delivery_charge']; ?>;

        const LONG_CHARGE =
            <?php echo $settings['long_delivery_charge']; ?>;

        const FREE_ORDER_AMOUNT =
            <?php echo $settings['free_delivery_order_amount']; ?>;

        function getDistance(
            lat1,
            lon1,
            lat2,
            lon2
        ) {

            const R = 6371;

            const dLat =
                (lat2 - lat1) *
                Math.PI / 180;

            const dLon =
                (lon2 - lon1) *
                Math.PI / 180;

            const a =

                Math.sin(dLat / 2) *
                Math.sin(dLat / 2)

                +

                Math.cos(lat1 * Math.PI / 180)

                *

                Math.cos(lat2 * Math.PI / 180)

                *

                Math.sin(dLon / 2)

                *

                Math.sin(dLon / 2);

            const c =

                2 *

                Math.atan2(
                    Math.sqrt(a),
                    Math.sqrt(1 - a)
                );

            return R * c;

        }
        let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];

        const cartContainer = document.getElementById('cartContainer');

        function saveCart() {
            localStorage.setItem(CART_KEY, JSON.stringify(cart));
        }

        function renderCart() {

            if (cart.length === 0) {

                cartContainer.innerHTML = `

<div class="empty-cart">
<i class="bi bi-bag-x"></i>
<h4>Your cart is empty</h4>
<p>Add products from retail page</p>
</div>

`;

                updateSummary();
                return;

            }

            let html = '';

            cart.forEach((item, index) => {

                html += `

<div class="cart-item">

<div class="item-left">

<div class="item-image">

<img
src="uploads/products/${item.image}"
style="
width:100%;
height:100%;
object-fit:cover;
border-radius:14px;
"
>

</div>

<div>
<div class="item-name">
${item.name}
</div>

<div class="item-price">
₹${item.price}
</div>
</div>

</div>

<div class="qty-box">

<button class="qty-btn"
onclick="decreaseQty(${index})">
-
</button>

<span>${item.qty}</span>

<button class="qty-btn"
onclick="increaseQty(${index})">
+
</button>

</div>

<div>
<button class="remove-btn"
onclick="removeItem(${index})">
Remove
</button>
</div>

</div>

`;

            });

            cartContainer.innerHTML = html;

            updateSummary();

        }

        function updateSummary() {

            let subtotal = 0;

            cart.forEach(item => {
                subtotal += item.price * item.qty;
            });

            let gst = 0;

            cart.forEach(item => {

                gst +=
                    (item.price * item.qty) *
                    (item.gst_rate / 100);

            });

            gst = Math.round(gst);

            let delivery =
                Number(
                    localStorage.getItem('deliveryCharge')
                ) || 0;

            if (subtotal >= FREE_ORDER_AMOUNT) {

                delivery = 0;

                localStorage.setItem(
                    'deliveryCharge',
                    0
                );

                if (
                    !sessionStorage.getItem(
                        'freeDeliveryShown'
                    )
                ) {

                    const freeModal =
                        new bootstrap.Modal(
                            document.getElementById(
                                'freeDeliveryModal'
                            )
                        );

                    freeModal.show();

                    sessionStorage.setItem(
                        'freeDeliveryShown',
                        'yes'
                    );

                }

            } else {

                sessionStorage.removeItem(
                    'freeDeliveryShown'
                );

            }

            let total = subtotal + gst + delivery;

            document.getElementById('subtotal').innerHTML =
                '₹' + subtotal;

            document.getElementById('gst').innerHTML =
                '₹' + gst;

            document.getElementById('delivery').innerHTML =
                '₹' + delivery;

            document.getElementById('grandTotal').innerHTML =
                '₹' + total;
        }



        function increaseQty(index) {
            cart[index].qty++;
            saveCart();
            renderCart();
        }

        function decreaseQty(index) {

            if (cart[index].qty > 1) {
                cart[index].qty--;
            } else {
                cart.splice(index, 1);
            }

            saveCart();
            renderCart();

        }

        function removeItem(index) {
            cart.splice(index, 1);
            saveCart();
            renderCart();
        }

        function calculateDelivery() {

            



            navigator.geolocation.getCurrentPosition(

                function(position) {

                    let distance =

                        getDistance(

                            SHOP_LAT,
                            SHOP_LNG,

                            position.coords.latitude,
                            position.coords.longitude

                        );

                    console.log(
                        "Distance =",
                        distance
                    );

                    console.log(
                        "FREE_KM =",
                        FREE_KM
                    );

                    console.log(
                        "MEDIUM_KM =",
                        MEDIUM_KM
                    );

                    let delivery = 0;

                    if (distance <= FREE_KM) {

                        delivery = MEDIUM_CHARGE;

                    } else if (distance <= MEDIUM_KM) {

                        delivery = MEDIUM_CHARGE;

                    } else {

                        delivery = LONG_CHARGE;

                    }

                    localStorage.setItem(
                        'deliveryCharge',
                        delivery
                    );

                    localStorage.setItem(
                        'distanceKM',
                        distance.toFixed(2)
                    );

                    sessionStorage.setItem(
                        'deliveryCalculated',
                        'yes'
                    );

                    renderCart();

                },

                function() {

                    localStorage.setItem(
                        'deliveryCharge',
                        LONG_CHARGE
                    );

                    sessionStorage.setItem(
                        'deliveryCalculated',
                        'yes'
                    );

                    renderCart();

                }

            );

        }
        calculateDelivery();
        renderCart();
    </script>



    <!-- Free Delivery Modal -->
    <div class="modal fade" id="freeDeliveryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <h3 class="text-success mb-3">
                    🎉 Free Delivery Unlocked!
                </h3>

                <p class="mb-0">
                    Congratulations! Your order qualifies for
                    <strong>FREE DELIVERY</strong>.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>