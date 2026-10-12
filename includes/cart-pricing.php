<?php

/*
|--------------------------------------------------------------------------
| Server-side cart pricing
|--------------------------------------------------------------------------
| WHY THIS FILE EXISTS
|
| The existing checkout takes the order total straight from the browser:
|
|     $total = $_POST['total'];            // checkout.php
|
| and that value is built in JavaScript from localStorage:
|
|     let total = subtotal + gst + delivery;
|     document.getElementById('totalInput').value = total;
|
| Anyone can open DevTools and set that to 1. With Cash On Delivery that
| is merely embarrassing — you notice at the door. With a live payment
| gateway it is money out of your pocket: the customer pays Rs 1, the
| signature verifies correctly (because they really did pay Rs 1), and
| the order is marked PAID.
|
| So for online payments the total is NEVER taken from the browser. The
| browser sends only product IDs and quantities. Everything else — unit
| price, discount, GST, delivery charge — is recalculated here from the
| database, and that is the amount charged.
|--------------------------------------------------------------------------
*/


/**
 * Rebuild a cart from the database.
 *
 * @param mysqli $conn
 * @param array  $rawCart   Items from the browser. Only 'id' and 'qty' are trusted.
 * @param float  $distanceKm
 *
 * @return array{
 *   ok: bool, error: string, items: array,
 *   subtotal: float, gst: float, delivery: float, total: float
 * }
 */
function priceCartServerSide($conn, $rawCart, $distanceKm = 0)
{
    $result = [
        'ok'       => false,
        'error'    => '',
        'items'    => [],
        'subtotal' => 0.0,
        'gst'      => 0.0,
        'delivery' => 0.0,
        'total'    => 0.0,
    ];

    if (!is_array($rawCart) || count($rawCart) === 0) {
        $result['error'] = 'Your cart is empty.';
        return $result;
    }

    if (count($rawCart) > 100) {
        $result['error'] = 'Too many different products in one order.';
        return $result;
    }

    $subtotal = 0.0;
    $gstTotal = 0.0;
    $items    = [];

    foreach ($rawCart as $row) {

        $productId = isset($row['id']) ? (int) $row['id'] : 0;
        $qty       = isset($row['qty']) ? (int) $row['qty'] : 0;

        if ($productId <= 0) {
            $result['error'] = 'One of the items in your cart is invalid.';
            return $result;
        }

        if ($qty <= 0 || $qty > 999) {
            $result['error'] = 'Invalid quantity for one of the items.';
            return $result;
        }

        $q = mysqli_query(
            $conn,
            "SELECT id, name, price, discount, gst_rate, stock, status
               FROM products
              WHERE id = $productId
              LIMIT 1"
        );

        $product = $q ? mysqli_fetch_assoc($q) : null;

        if (!$product) {
            $result['error'] = 'A product in your cart no longer exists. '
                . 'Please remove it and try again.';
            return $result;
        }

        if ((int) $product['status'] !== 1) {
            $result['error'] = '"' . $product['name'] . '" is no longer available.';
            return $result;
        }

        if ((int) $product['stock'] < $qty) {
            $result['error'] = 'Only ' . (int) $product['stock']
                . ' left in stock for "' . $product['name'] . '".';
            return $result;
        }

        // Same discount formula product.php uses on the detail page,
        // so the price the customer saw is the price they are charged.
        $basePrice = (float) $product['price'];
        $discount  = (float) $product['discount'];

        $unitPrice = $discount > 0
            ? $basePrice - ($basePrice * $discount / 100)
            : $basePrice;

        $unitPrice = round($unitPrice, 2);

        $lineTotal = round($unitPrice * $qty, 2);
        $lineGst   = round($lineTotal * ((float) $product['gst_rate'] / 100), 2);

        $subtotal += $lineTotal;
        $gstTotal += $lineGst;

        $items[] = [
            'id'       => (int) $product['id'],
            'name'     => $product['name'],
            'price'    => $unitPrice,
            'gst_rate' => (float) $product['gst_rate'],
            'qty'      => $qty,
            'total'    => $lineTotal,
        ];
    }

    $delivery = deliveryChargeServerSide($conn, $subtotal, $distanceKm);

    $result['ok']       = true;
    $result['items']    = $items;
    $result['subtotal'] = round($subtotal, 2);
    $result['gst']      = round($gstTotal, 2);
    $result['delivery'] = round($delivery, 2);
    $result['total']    = round($subtotal + $gstTotal + $delivery, 2);

    return $result;
}


/**
 * Delivery charge, using the distance bands already stored in settings.
 * The browser can lie about distance, so the free-delivery-by-order-value
 * rule is applied on top and the distance is clamped to something sane.
 */
function deliveryChargeServerSide($conn, $subtotal, $distanceKm)
{
    $q = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");

    $s = $q ? mysqli_fetch_assoc($q) : null;

    if (!$s) {
        return 0.0;
    }

    // Free delivery above a certain order value
    $freeAbove = (float) $s['free_delivery_order_amount'];

    if ($freeAbove > 0 && $subtotal >= $freeAbove) {
        return 0.0;
    }

    $distanceKm = (float) $distanceKm;

    if ($distanceKm < 0 || $distanceKm > 5000) {
        $distanceKm = 0;
    }

    if ($distanceKm <= (float) $s['free_delivery_km']) {
        return (float) $s['free_delivery_charge'];
    }

    if ($distanceKm <= (float) $s['medium_delivery_km']) {
        return (float) $s['medium_delivery_charge'];
    }

    return (float) $s['long_delivery_charge'];
}
