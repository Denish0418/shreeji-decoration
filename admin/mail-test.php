<?php

/*
|--------------------------------------------------------------------------
| Admin > Email Diagnostics
|--------------------------------------------------------------------------
| Open this page ON THE LIVE SERVER (not localhost) at:
|
|     https://yourdomain.com/admin/mail-test.php
|
| It tells you in plain language whether the server can send email at all,
| which ports the host has blocked, and - if a send fails - the exact
| reason, instead of the useless "Unable to send OTP" message.
|
| Delete this file, or leave it (it is admin-login protected), once
| everything is working.
|--------------------------------------------------------------------------
*/

session_start();

require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

require_once '../includes/mail.php';

$settings = mailSettings($conn);


/* -------------------------------------------------------------------
   Can this server open an outbound TCP connection to host:port?
------------------------------------------------------------------- */
function probePort($host, $port, $timeout = 6)
{
    $start = microtime(true);

    $socket = @fsockopen($host, $port, $errNo, $errStr, $timeout);

    $ms = round((microtime(true) - $start) * 1000);

    if ($socket) {
        fclose($socket);
        return ['open' => true, 'ms' => $ms, 'error' => ''];
    }

    return [
        'open'  => false,
        'ms'    => $ms,
        'error' => trim($errStr) !== '' ? "$errStr (code $errNo)" : 'connection refused or timed out',
    ];
}


/* -------------------------------------------------------------------
   Run the checks
------------------------------------------------------------------- */
$checks = [];

$checks['php_version'] = [
    'label' => 'PHP version',
    'ok'    => version_compare(PHP_VERSION, '7.4', '>='),
    'value' => PHP_VERSION,
    'hint'  => 'PHPMailer needs PHP 7.4 or newer.',
];

$checks['curl'] = [
    'label' => 'cURL extension',
    'ok'    => function_exists('curl_init'),
    'value' => function_exists('curl_init') ? 'Installed' : 'MISSING',
    'hint'  => 'Needed for the Brevo API method. Ask your host to enable php-curl.',
];

$checks['openssl'] = [
    'label' => 'OpenSSL extension',
    'ok'    => extension_loaded('openssl'),
    'value' => extension_loaded('openssl') ? 'Installed' : 'MISSING',
    'hint'  => 'Needed for TLS. Ask your host to enable php-openssl.',
];

$checks['vendor'] = [
    'label' => 'PHPMailer library',
    'ok'    => class_exists('PHPMailer\\PHPMailer\\PHPMailer'),
    'value' => class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? 'Loaded' : 'MISSING',
    'hint'  => 'Upload the whole vendor/ folder to the server.',
];

$logPath = mailLogPath();

$checks['log_writable'] = [
    'label' => 'Mail log writable',
    'ok'    => is_writable(dirname($logPath)),
    'value' => is_writable(dirname($logPath)) ? dirname($logPath) : 'NOT WRITABLE: ' . dirname($logPath),
    'hint'  => 'chmod 755 that folder so failures can be logged.',
];


/* Port probes - the decisive test */
$ports = [];

$ports['brevo_https'] = [
    'label'  => 'HTTPS to api.brevo.com:443',
    'result' => probePort('api.brevo.com', 443),
    'note'   => 'Used by the Brevo API method. This is almost never blocked.',
];

$ports['gmail_587'] = [
    'label'  => 'SMTP to smtp.gmail.com:587',
    'result' => probePort('smtp.gmail.com', 587),
    'note'   => 'Used by the old Gmail SMTP method. Commonly blocked by hosts.',
];

$ports['gmail_465'] = [
    'label'  => 'SMTPS to smtp.gmail.com:465',
    'result' => probePort('smtp.gmail.com', 465),
    'note'   => 'Alternate SMTP port. Also commonly blocked.',
];

$isBrevo = usingBrevoApi($settings);


/* -------------------------------------------------------------------
   Handle the test send
------------------------------------------------------------------- */
$testResult = null;

if (isset($_POST['send_test'])) {

    $to = trim($_POST['test_email']);

    $error = null;

    $ok = sendAppMail(
        $conn,
        $to,
        'Test Recipient',
        'Test email from Shreeji Decoration',
        '<h2 style="color:#8B4513">It works.</h2>
         <p>This test email was sent from your live server at '
         . date('d M Y, H:i:s') . '.</p>
         <p>Transport used: <b>' . ($isBrevo ? 'Brevo HTTP API' : 'SMTP') . '</b></p>',
        '',
        $error
    );

    $testResult = ['ok' => $ok, 'error' => $error, 'to' => $to];
}


/* Tail of the log */
$logTail = '';

if (file_exists($logPath)) {
    $lines   = @file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $logTail = $lines ? implode("\n", array_slice($lines, -25)) : '';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Diagnostics - Shreeji Decoration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f3ef; }
        .wrap { max-width: 900px; margin: 30px auto; }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,.06); margin-bottom: 22px; }
        .card-header { background: #8B4513; color: #fff; font-weight: 600; }
        .mono { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 13px; }
        pre.log { background: #1e1e1e; color: #ddd; padding: 14px; border-radius: 6px;
                  font-size: 12px; max-height: 320px; overflow: auto; }
        .pill { font-size: 12px; padding: 3px 10px; border-radius: 20px; }
    </style>
</head>
<body>
<div class="wrap">

    <h3 class="mb-1">Email Diagnostics</h3>
    <p class="text-muted">
        Run this page on the live server. Host:
        <span class="mono"><?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'unknown') ?></span>
    </p>

    <!-- CURRENT METHOD -->
    <div class="card">
        <div class="card-header">Current sending method</div>
        <div class="card-body">
            <?php if ($isBrevo) { ?>
                <p class="mb-2">
                    <span class="badge bg-success">Brevo HTTP API</span>
                    Mail is sent over HTTPS on port 443. This works on blocked-SMTP hosts.
                </p>
            <?php } else { ?>
                <p class="mb-2">
                    <span class="badge bg-warning text-dark">Classic SMTP</span>
                    Mail is sent to
                    <span class="mono"><?= htmlspecialchars($settings['smtp_host'] ?? '-') ?>:<?= htmlspecialchars($settings['smtp_port'] ?? '-') ?></span>.
                </p>
                <div class="alert alert-warning mb-0">
                    <b>This is most likely why your emails stop working after hosting.</b>
                    Look at the port test below. If port 587 and 465 are both blocked,
                    SMTP can never work on this server no matter what you change in the code.
                    Switch to the Brevo API method using the steps at the bottom of this page.
                </div>
            <?php } ?>
            <p class="mb-0 mt-3 mono text-muted">
                Sender address: <?= htmlspecialchars($settings['smtp_email'] ?? '(not set)') ?>
            </p>
        </div>
    </div>

    <!-- PORT TEST -->
    <div class="card">
        <div class="card-header">Outbound connection test (the important one)</div>
        <div class="card-body">
            <table class="table table-sm align-middle mb-0">
                <?php foreach ($ports as $p) {
                    $r = $p['result']; ?>
                    <tr>
                        <td style="width:45%"><?= htmlspecialchars($p['label']) ?></td>
                        <td style="width:20%">
                            <?php if ($r['open']) { ?>
                                <span class="pill bg-success text-white">OPEN (<?= $r['ms'] ?> ms)</span>
                            <?php } else { ?>
                                <span class="pill bg-danger text-white">BLOCKED</span>
                            <?php } ?>
                        </td>
                        <td class="small text-muted">
                            <?= htmlspecialchars($p['note']) ?>
                            <?php if (!$r['open']) { ?>
                                <br><span class="mono"><?= htmlspecialchars($r['error']) ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <!-- ENVIRONMENT -->
    <div class="card">
        <div class="card-header">Server environment</div>
        <div class="card-body">
            <table class="table table-sm mb-0">
                <?php foreach ($checks as $c) { ?>
                    <tr>
                        <td style="width:30%"><?= htmlspecialchars($c['label']) ?></td>
                        <td style="width:25%">
                            <span class="pill <?= $c['ok'] ? 'bg-success' : 'bg-danger' ?> text-white">
                                <?= $c['ok'] ? 'OK' : 'PROBLEM' ?>
                            </span>
                        </td>
                        <td class="small mono"><?= htmlspecialchars($c['value']) ?>
                            <?php if (!$c['ok']) { ?>
                                <br><span class="text-danger"><?= htmlspecialchars($c['hint']) ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <!-- TEST SEND -->
    <div class="card">
        <div class="card-header">Send a real test email</div>
        <div class="card-body">

            <?php if ($testResult) { ?>
                <?php if ($testResult['ok']) { ?>
                    <div class="alert alert-success">
                        Sent successfully to <b><?= htmlspecialchars($testResult['to']) ?></b>.
                        Check the inbox, and the spam folder too.
                    </div>
                <?php } else { ?>
                    <div class="alert alert-danger">
                        <b>Send failed.</b><br>
                        <?= htmlspecialchars($testResult['error']) ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <form method="post" class="row g-2">
                <div class="col-sm-8">
                    <input type="email" name="test_email" class="form-control"
                           placeholder="your@email.com" required
                           value="<?= htmlspecialchars($settings['email'] ?? '') ?>">
                </div>
                <div class="col-sm-4">
                    <button name="send_test" class="btn btn-dark w-100">Send test email</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FIX STEPS -->
    <div class="card">
        <div class="card-header">How to switch to the Brevo API method</div>
        <div class="card-body">
            <ol class="mb-0">
                <li class="mb-2">Create a free account at <b>brevo.com</b> (300 emails/day, no card needed).</li>
                <li class="mb-2">Go to <b>Senders, Domains &amp; Dedicated IPs &rarr; Senders</b>,
                    add <span class="mono"><?= htmlspecialchars($settings['smtp_email'] ?? 'your@email.com') ?></span>,
                    and click the confirmation link Brevo emails you. Skipping this
                    step is the #1 cause of "sender not valid" errors.</li>
                <li class="mb-2">Go to <b>SMTP &amp; API &rarr; API Keys</b>, click
                    <b>Generate a new API key</b>, and copy it. It starts with
                    <span class="mono">xkeysib-</span>.</li>
                <li class="mb-2">In <b>Admin &rarr; Settings</b>, paste that key into the
                    <b>SMTP Password</b> field and save. Leave the other SMTP fields as they are.</li>
                <li>Reload this page and click <b>Send test email</b> again. The method
                    at the top should now read "Brevo HTTP API".</li>
            </ol>
        </div>
    </div>

    <!-- LOG -->
    <div class="card">
        <div class="card-header">Recent mail log</div>
        <div class="card-body">
            <p class="small text-muted mono mb-2"><?= htmlspecialchars($logPath) ?></p>
            <pre class="log"><?= htmlspecialchars($logTail ?: 'No entries yet.') ?></pre>
        </div>
    </div>

    <a href="settings.php" class="btn btn-outline-secondary">Back to Settings</a>

</div>
</body>
</html>
