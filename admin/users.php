<?php

session_start();

require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$users = mysqli_query(
    $conn,
    "SELECT * FROM users ORDER BY id DESC"
);

?>
<!DOCTYPE html>
<html>

<head>

    <title>Users</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
        }

        /* sidebar */
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
            overflow-x: hidden;

            scrollbar-width: thin;
            scrollbar-color: #7A2E0D #2d140a;
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

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #7A2E0D;
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

        .users-card {
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

        /* avatar */
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #EFE3D3;
            color: #7A3B00;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* role badge */
        .role-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .admin-role {
            background: #EFE3D3;
            color: #7A3B00;
        }

        .user-role {
            background: #DDEFE2;
            color: #2F6B3E;
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

    <!-- SIDEBAR -->

    <?php include 'includes/sidebar.php'; ?>

    <!-- MAIN -->

    <div class="main-content">

        <div class="top-bar">

            <div>
                <h2 class="fw-bold mb-1">Users</h2>
                <p class="text-muted mb-0" style="font-size:14px;">
                    Manage website users
                </p>
            </div>

        </div>

        <!-- STATS -->

        <div class="row g-4 mb-4">

            <div class="col-lg-4">

                <div class="stat-card">

                    <h3 class="fw-bold">
                        <?php echo mysqli_num_rows($users); ?>
                    </h3>

                    <p>Total Users</p>

                </div>

            </div>

        </div>

        <!-- USERS TABLE -->

        <div class="users-card">

            <div class="search-wrap">

                <i class="bi bi-search"></i>

                <input type="text"
                    placeholder="Search Users...">

            </div>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = mysqli_fetch_assoc($users)) { ?>

                            <tr>

                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="user-avatar">

                                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>

                                        </div>

                                        <div>

                                            <div class="fw-semibold">

                                                <?php echo $row['name']; ?>

                                            </div>

                                            <div class="text-muted" style="font-size:12px;">

                                                User ID:
                                                #<?php echo $row['id']; ?>

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <?php echo $row['email']; ?>

                                </td>

                                <td>

                                    <?php echo $row['phone']; ?>

                                </td>

                                <td>

                                    <?php if ($row['role'] == 'admin') { ?>

                                        <span class="role-badge admin-role">

                                            Admin

                                        </span>

                                    <?php } else { ?>

                                        <span class="role-badge user-role">

                                            User

                                        </span>

                                    <?php } ?>

                                </td>

                                <td>

                                    <a href="view-user.php?id=<?php echo $row['id']; ?>"
                                        class="action-btn view-btn">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                    <a href="delete-user.php?id=<?php echo $row['id']; ?>"
                                        class="action-btn delete-btn">

                                        <i class="bi bi-trash"></i>

                                    </a>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</body>

</html>