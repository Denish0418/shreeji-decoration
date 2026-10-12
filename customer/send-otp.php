<?php

/*
|--------------------------------------------------------------------------
| Prevent any PHP warning/notice/fatal error from ever being printed
| directly into the response. If ANY of that happens, the JSON below
| becomes invalid and the browser shows a generic "Unable to connect"
| error instead of the real problem. Everything is wrapped so the
| response is always valid JSON, and the real error (if any) is written
| to the PHP error log instead, where it can actually be diagnosed.
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

try {

    require_once '../config/db.php';
    require_once '../includes/mail.php';

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {

        echo json_encode([
            "success" => false,
            "message" => "Invalid Request"
        ]);

        exit;
    }

    /* -------------------------
       Get Customer Details
    -------------------------- */

    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    /* -------------------------
       Validation
    -------------------------- */

    if ($name == '' || $phone == '' || $email == '') {

        echo json_encode([
            "success" => false,
            "message" => "Please fill all fields."
        ]);

        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid Email Address."
        ]);

        exit;
    }

    /* -------------------------
       Generate OTP
    -------------------------- */

    $otp = random_int(100000, 999999);

    /* -------------------------
       Check Existing Customer
    -------------------------- */

    $check = mysqli_query(
        $conn,
        "SELECT id FROM customers WHERE email='".mysqli_real_escape_string($conn,$email)."' LIMIT 1"
    );

    $dbWriteOk = true;

    if (mysqli_num_rows($check) > 0) {

        $dbWriteOk = mysqli_query(
            $conn,
            "UPDATE customers SET

                name='".mysqli_real_escape_string($conn,$name)."',
                phone='".mysqli_real_escape_string($conn,$phone)."',
                otp='".$otp."',
                is_verified='0'

            WHERE email='".mysqli_real_escape_string($conn,$email)."'"
        );

    } else {

        /* -------------------------
           This email is new, but the phone number might already
           belong to a different account (phone has its own UNIQUE
           key). Check for that BEFORE inserting, otherwise the
           INSERT silently fails (duplicate key), no row is ever
           created for this email, the OTP email still gets sent,
           and the user hits "Customer not found" at verification.
        -------------------------- */

        $phoneCheck = mysqli_query(
            $conn,
            "SELECT id, email FROM customers WHERE phone='".mysqli_real_escape_string($conn,$phone)."' LIMIT 1"
        );

        if (mysqli_num_rows($phoneCheck) > 0) {

            echo json_encode([
                "success" => false,
                "message" => "This phone number is already registered with a different email address. Please log in using that email instead."
            ]);

            exit;
        }

        $dbWriteOk = mysqli_query(
            $conn,
            "INSERT INTO customers
            (
                name,
                phone,
                email,
                otp,
                is_verified,
                status
            )
            VALUES
            (
                '".mysqli_real_escape_string($conn,$name)."',
                '".mysqli_real_escape_string($conn,$phone)."',
                '".mysqli_real_escape_string($conn,$email)."',
                '".$otp."',
                0,
                1
            )"
        );

    }

    if (!$dbWriteOk) {

        error_log('send-otp.php DB write failed: ' . mysqli_error($conn));

        echo json_encode([
            "success" => false,
            "message" => "Unable to process your request right now. Please try again in a moment."
        ]);

        exit;
    }

    /* -------------------------
       Send OTP Email
    -------------------------- */

    $mailError = null;

    $mailSent = sendOTPEmail(
        $conn,
        $name,
        $email,
        $otp,
        $mailError
    );

    // The real reason is written to the mail log and shown in
    // admin/mail-test.php. The customer only sees a friendly message.
    if (!$mailSent) {
        error_log('send-otp.php mail failed: ' . $mailError);
    }

    if (!$mailSent) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to send OTP. Please check your email address and try again, or contact support if this keeps happening."
        ]);

        exit;
    }

    /* -------------------------
       Success
    -------------------------- */

    echo json_encode([
        "success" => true,
        "message" => "OTP Sent Successfully."
    ]);

} catch (\Throwable $e) {

    error_log('send-otp.php failed: ' . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "A server error occurred while sending the OTP. Please try again in a moment."
    ]);
}