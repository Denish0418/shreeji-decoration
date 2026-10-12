<?php
session_start();
require_once '../config/db.php';

mysqli_query(
    $conn,
    "UPDATE product_reviews
     SET admin_seen='1'
     WHERE admin_seen='0'"
);

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

/* APPROVE REVIEW */


/* REJECT REVIEW */


/* DELETE REVIEW */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    mysqli_query(
        $conn,
        "DELETE FROM product_reviews
        WHERE id='$id'"
    );

    header("Location: reviews.php");
    exit;
}

$reviews =
    mysqli_query(
        $conn,
        "SELECT *
    FROM product_reviews
    ORDER BY id DESC"
    );
?>

<!DOCTYPE html>
<html>

<head>

    <title>Reviews</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 270px;
            height: 100vh;
            background: #2d140a;
            padding: 30px;
            color: #fff;
            overflow-y: auto;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 40px;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
            padding: 14px 18px;
            border-radius: 14px;
        }

        .sidebar ul li a:hover {
            background: #7A2E0D;
        }

        .sidebar ul li a.active {
            background: #7A2E0D;
        }

        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

        .review-card {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
        }

        .star {
            color: #ffc107;
            font-size: 18px;
        }

        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
        }

        
        /* main */
        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

        /* cards */
        .stat-card {
            background: #fff;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-card i {
            font-size: 38px;
            color: #7A2E0D;
        }

        .orders-card {
            background: #fff;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        /* top bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* search */
        .search-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f5f2;
            border: 1.5px solid #ede8e3;
            border-radius: 14px;
            padding: 10px 16px;
            margin-bottom: 24px;
        }

        .search-wrap input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
        }

        /* table */
        .table {
            margin: 0;
            vertical-align: middle;
        }

        .table thead th {
            border: none;
            padding: 16px 18px;
            font-size: 13px;
            font-weight: 600;
            color: #9CA3AF;
            background: #f8f5f2;
        }

        .table tbody td {
            padding: 14px 18px;
            border-color: #f3f0ec;
            font-size: 14px;
        }

        /* status */
        .status-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .pending {
            background: #F6E7C8;
            color: #8B5E00;
        }

        .return-requested {
            background: #FFF3CD;
            color: #856404;
        }

        .return-approved {
            background: #D4EDDA;
            color: #155724;
        }

        .return-rejected {
            background: #F8D7DA;
            color: #721C24;
        }

        .completed {
            background: #DDEFE2;
            color: #2F6B3E;
        }

        .cancelled {
            background: #F5D7D7;
            color: #9B1C1C;
        }

        /* buttons */
        .action-btn {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .view-btn {
            background: #DDEFE2;
            color: #2F6B3E;
        }

        .delete-btn {
            background: #F5D7D7;
            color: #9B1C1C;
        }
    </style>

</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <h2 class="fw-bold mb-4">
            ⭐ Product Reviews
        </h2>
        <?php while ($review = mysqli_fetch_assoc($reviews)) { ?>
            <div class="review-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>
                            <?php echo $review['customer_name']; ?>
                        </h5>
                        <p class="text-muted mb-1">
                            <?php echo $review['phone']; ?>
                        </p>
                    </div>
                    <div>
                        <?php
                        $statusColor = 'warning';

                        if ($review['status'] == 'Approved') {
                            $statusColor = 'success';
                        }

                        if ($review['status'] == 'Rejected') {
                            $statusColor = 'danger';
                        }
                        ?>
                        <span class="badge bg-<?php echo $statusColor; ?>">
                            <?php echo $review['status']; ?>
                        </span>
                    </div>
                </div>
                <div class="my-2">
                    <?php
                    for ($i = 1; $i <= $review['rating']; $i++) {
                        echo '<i class="bi bi-star-fill star"></i>';
                    }
                    ?>
                </div>
                <p>
                    <?php echo nl2br($review['review']); ?>
                </p>

                <div class="mt-3">
                    <a href="?delete=<?php echo $review['id']; ?>"
                        class="btn btn-dark btn-sm">
                        Delete
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</body>

</html>