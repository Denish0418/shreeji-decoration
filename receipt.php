<?php

require_once 'includes/session.php';
require_once 'config/db.php';

$order_id = $_GET['order_id'];

$query = mysqli_query($conn, "
SELECT * FROM orders
WHERE order_id='$order_id'
");

$order = mysqli_fetch_assoc($query);

$products = json_decode(
    $order['products'],
    true
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Receipt</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #FDF8F0;
            font-family: Poppins;
            padding: 40px;
        }

        .receipt-box {
            max-width: 850px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .08);
        }

        .receipt-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .order-badge {
            display: inline-block;
            background: #FFF3E0;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            color: #B8863F;
            margin-bottom: 25px;
        }

        .table img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 14px;
        }

        .total {
            text-align: right;
            font-size: 1.7rem;
            font-weight: 800;
            color: #B8863F;
            margin-top: 25px;
        }

        .download-btn {
            background: linear-gradient(135deg, #C9A063, #B8863F);
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 700;
            color: #fff;
            margin-top: 30px;
        }
    </style>

</head>

<body>

    <div class="receipt-box">

        <div class="receipt-title">
            Shreeji Decoration
        </div>

        <div class="order-badge">
            Order ID :
            #<?php echo $order['order_id']; ?>
        </div>

        <div class="alert alert-success text-center mb-4">

            <h3>🎉 Order Placed Successfully</h3>

            <p>
                Thank you for shopping with
                Shreeji Decoration.
            </p>

        </div>

        <h5>Customer Details</h5>

        <p>
            <strong>Name:</strong>
            <?php echo $order['customer_name']; ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?php echo $order['phone']; ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo $order['email']; ?>
        </p>

        <p>
            <strong>Address:</strong>
            <?php echo $order['address']; ?>
        </p>

        <hr>

        <h5>Products</h5>

        <table class="table">

            <thead>

                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>

            </thead>

            <tbody>

                <?php

                $total = 0;

                foreach ($products as $item) {

                    $total += $item['price'] * $item['qty'];

                ?>

                    <tr>

                        <td>
                            <img src="uploads/products/<?php echo $item['image']; ?>">
                        </td>

                        <td>
                            <?php echo $item['name']; ?>
                        </td>

                        <td>
                            <?php echo $item['qty']; ?>
                        </td>

                        <td>
                            ₹<?php echo $item['price']; ?>
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

        <div class="total">
            Total : ₹<?php echo $total; ?>
        </div>

        <div style="

display:flex;
justify-content:space-between;
align-items:center;
margin-top:35px;
flex-wrap:wrap;
gap:20px;

">

            <button
                onclick="downloadReceipt()"
                class="download-btn">

                Download Receipt

            </button>

            <a href="retail.php?refresh=1"

                style="

background:#2A1800;
color:#C9A063;

padding:14px 28px;

border-radius:50px;

text-decoration:none;

font-weight:700;

display:flex;
align-items:center;
gap:10px;

transition:0.3s;

"

                onmouseover="this.style.background='#7A2E0D'"
                onmouseout="this.style.background='#2A1800'">

                ← Back To Home

            </a>

        </div>



    </div>

    <script>
        const customerId =
            <?= isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0 ?>;

        const CART_KEY = "CART_KEY" + customerId;

        // The order is already placed by the time this page loads,
        // so the cart should be cleared right away - not only when
        // the customer happens to click "Download Receipt".
        localStorage.removeItem(CART_KEY);

        function downloadReceipt() {

            window.print();

        }
    </script>



</body>

</html>