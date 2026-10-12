<?php

require_once '../includes/session.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['customer_logged_in'])) {
    redirect("../retail.php");
}

$customer_id = (int) $_SESSION['customer_id'];
$errors = [];
$success = false;

/* UPDATE PROFILE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') {
        $errors[] = "Name is required.";
    }

    if ($phone === '' || !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Please enter a valid 10-digit phone number.";
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($errors)) {

        $emailEsc = mysqli_real_escape_string($conn, $email);

        /* make sure this email isn't already used by another customer */
        $check = mysqli_query(
            $conn,
            "SELECT id FROM customers
             WHERE email='$emailEsc' AND id != $customer_id
             LIMIT 1"
        );

        if (mysqli_num_rows($check) > 0) {
            $errors[] = "This email address is already linked to another account.";
        }
    }

    if (empty($errors)) {

        $nameEsc    = mysqli_real_escape_string($conn, $name);
        $phoneEsc   = mysqli_real_escape_string($conn, $phone);
        $emailEsc   = mysqli_real_escape_string($conn, $email);
        $addressEsc = mysqli_real_escape_string($conn, $address);

        mysqli_query(
            $conn,
            "UPDATE customers
             SET name='$nameEsc', phone='$phoneEsc', email='$emailEsc', address='$addressEsc'
             WHERE id=$customer_id"
        );

        $_SESSION['customer_name']  = $name;
        $_SESSION['customer_phone'] = $phone;
        $_SESSION['customer_email'] = $email;

        $success = true;
    }
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM customers WHERE id=$customer_id LIMIT 1"
);

$customer = mysqli_fetch_assoc($result);

if (!$customer) {
    session_unset();
    session_destroy();
    redirect("../retail.php");
}

/* Order count for a quick stat */
$orderCountResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM orders WHERE customer_id=$customer_id"
);
$orderCount = mysqli_fetch_assoc($orderCountResult)['total'] ?? 0;

/* Total amount spent */
$totalSpentResult = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE customer_id=$customer_id"
);
$totalSpent = mysqli_fetch_assoc($totalSpentResult)['total'] ?? 0;

/* Most recent order date */
$lastOrderResult = mysqli_query(
    $conn,
    "SELECT created_at FROM orders WHERE customer_id=$customer_id ORDER BY id DESC LIMIT 1"
);
$lastOrderRow = mysqli_fetch_assoc($lastOrderResult);
$lastOrderDate = $lastOrderRow ? $lastOrderRow['created_at'] : null;

/* Wishlist count */
$wishlistCountResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM wishlist WHERE user_id=$customer_id"
);
$wishlistCount = mysqli_fetch_assoc($wishlistCountResult)['total'] ?? 0;

/* Previous addresses - same logic as checkout.php, so the customer
   sees the exact same saved addresses here as at checkout time. */
$previousAddresses = [];

$prevAddressQuery = mysqli_query(
    $conn,
    "SELECT DISTINCT address FROM orders
     WHERE customer_id = $customer_id
     ORDER BY id DESC
     LIMIT 20"
);

if ($prevAddressQuery) {
    while ($row = mysqli_fetch_assoc($prevAddressQuery)) {
        if (trim($row['address']) === '') {
            continue;
        }
        if (in_array($row['address'], $previousAddresses, true)) {
            continue;
        }
        $previousAddresses[] = $row['address'];
        if (count($previousAddresses) >= 5) {
            break;
        }
    }
}

$profileLetter = strtoupper(substr($customer['name'] ?? 'U', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: #FDF8F0;
            font-family: 'Poppins', sans-serif;
        }

        .top-strip {
            background: #2B1600;
            padding: 16px 0;
        }

        .top-strip a {
            color: #C9A063;
            text-decoration: none;
            font-size: .9rem;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #B8863F, #8F6428);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 auto 14px;
        }

        .profile-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }

        .stat-box {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
        }

        .stat-box .num {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: #8F6428;
        }

        .form-label {
            font-weight: 600;
            font-size: .85rem;
            color: #444;
        }

        .btn-update {
            background: #2B1600;
            color: #fff;
            border-radius: 12px;
            padding: 10px 28px;
        }

        .btn-update:hover {
            background: #7A2E0D;
            color: #fff;
        }

        .account-status-badge {
            font-size: .75rem;
            padding: 4px 12px;
            border-radius: 30px;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="top-strip">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../retail.php"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
            <a href="logout.php" class="text-danger" style="color:#e07a7a !important;">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>

    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-lg-9">

                <h1 class="mb-4 text-center" style="font-family:'Playfair Display',serif;font-weight:700;">
                    My Profile
                </h1>

                <?php if ($success) { ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-1"></i> Profile updated successfully.
                    </div>
                <?php } ?>

                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error) { ?>
                                <li><?= htmlspecialchars($error); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <div class="row g-4">

                    <!-- SUMMARY -->
                    <div class="col-md-4">
                        <div class="card profile-card p-4 text-center h-100">
                            <div class="profile-avatar"><?= htmlspecialchars($profileLetter); ?></div>
                            <h5 class="mb-1"><?= htmlspecialchars($customer['name']); ?></h5>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($customer['email']); ?></p>

                            <span class="account-status-badge mb-3"
                                style="background:<?= $customer['status'] === 'Active' ? '#e6f7ec' : '#fbeaea'; ?>;
                                       color:<?= $customer['status'] === 'Active' ? '#1a7f3c' : '#c0392b'; ?>;">
                                <?= htmlspecialchars($customer['status'] ?? 'Active'); ?> Account
                            </span>

                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="num"><?= (int) $orderCount; ?></div>
                                        <div class="small text-muted">Orders</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="num">
                                            <?= !empty($customer['created_at']) ? date('Y', strtotime($customer['created_at'])) : '-'; ?>
                                        </div>
                                        <div class="small text-muted">Member Since</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="num" style="font-size:1.2rem;">₹<?= number_format($totalSpent, 0); ?></div>
                                        <div class="small text-muted">Total Spent</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <div class="num"><?= (int) $wishlistCount; ?></div>
                                        <div class="small text-muted">Wishlist</div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($lastOrderDate) { ?>
                                <p class="text-muted small mt-3 mb-0">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Last order on <?= date('d M Y', strtotime($lastOrderDate)); ?>
                                </p>
                            <?php } ?>

                            <a href="../my-orders.php" class="btn btn-outline-dark rounded-pill mt-3">
                                <i class="bi bi-box-seam me-1"></i> View My Orders
                            </a>
                        </div>
                    </div>

                    <!-- EDIT FORM -->
                    <div class="col-md-8">
                        <div class="card profile-card p-4 h-100">
                            <h5 class="mb-4 fw-bold">Account Details</h5>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="<?= htmlspecialchars($customer['name']); ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" maxlength="10"
                                            value="<?= htmlspecialchars($customer['phone']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?= htmlspecialchars($customer['email']); ?>" required>
                                        <div class="form-text">Used for login OTP — changing this changes your login email.</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>

                                    <?php if (!empty($previousAddresses)) { ?>
                                        <select id="addressDropdown" class="form-control mb-2" onchange="fillAddressFromDropdown()">
                                            <option value="">-- Select a previous address --</option>
                                            <?php foreach ($previousAddresses as $i => $addr) {
                                                if (function_exists('mb_strlen')) {
                                                    $shortAddr = mb_strlen($addr) > 60 ? mb_substr($addr, 0, 60) . '...' : $addr;
                                                } else {
                                                    $shortAddr = strlen($addr) > 60 ? substr($addr, 0, 60) . '...' : $addr;
                                                }
                                                $shortAddr = preg_replace('/\s+/', ' ', $shortAddr);
                                            ?>
                                                <option value="<?= $i; ?>"><?= htmlspecialchars($shortAddr); ?><?= $i === 0 ? ' (Most Recent)' : ''; ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>

                                    <textarea name="address" id="addressTextarea" class="form-control" rows="3"
                                        placeholder="Add your delivery address"><?= htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                                    <div class="form-text">
                                        <?php if (!empty($previousAddresses)) { ?>
                                            Pick a previous address above to fill this in, or type a new one.
                                        <?php } else { ?>
                                            This is saved to your profile as your default address.
                                        <?php } ?>
                                    </div>
                                </div>

                                <button type="submit" name="update_profile" class="btn btn-update">
                                    <i class="bi bi-check2 me-1"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <?php if (!empty($previousAddresses)) { ?>
        <script>
            const savedAddresses = <?= json_encode(array_values($previousAddresses)); ?>;

            function fillAddressFromDropdown() {
                let dropdown = document.getElementById('addressDropdown');
                let index = dropdown.value;

                if (index === '') {
                    return;
                }

                document.getElementById('addressTextarea').value = savedAddresses[parseInt(index)] || '';
            }
        </script>
    <?php } ?>

</body>

</html>