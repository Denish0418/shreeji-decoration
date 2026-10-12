<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';


function mailLogPath()
{
    $preferred = dirname(__DIR__, 2) . '/shreeji-logs';

    if (!is_dir($preferred)) {
        @mkdir($preferred, 0755, true);
    }

    if (is_dir($preferred) && is_writable($preferred)) {
        return $preferred . '/mail.log';
    }

    // Fallback: keep it inside includes/ but block web access
    $fallbackDir = __DIR__ . '/logs';

    if (!is_dir($fallbackDir)) {
        @mkdir($fallbackDir, 0755, true);
    }

    $htaccess = $fallbackDir . '/.htaccess';

    if (is_dir($fallbackDir) && !file_exists($htaccess)) {
        @file_put_contents($htaccess, "Require all denied\nDeny from all\n");
    }

    return $fallbackDir . '/mail.log';
}


function mailLog($message)
{
    @file_put_contents(
        mailLogPath(),
        '[' . date('Y-m-d H:i:s') . '] ' . trim($message) . "\n",
        FILE_APPEND
    );
}


/* -------------------------------------------------------------------
   Load the settings row once per request.
------------------------------------------------------------------- */
function mailSettings($conn)
{
    static $cached = null;

    if ($cached !== null) {
        return $cached;
    }

    $result = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");

    $cached = $result ? mysqli_fetch_assoc($result) : null;

    return $cached;
}


/* -------------------------------------------------------------------
   Is the saved password actually a Brevo API key?
------------------------------------------------------------------- */
function usingBrevoApi($settings)
{
    return !empty($settings['smtp_password'])
        && strpos(trim($settings['smtp_password']), 'xkeysib-') === 0;
}


/* -------------------------------------------------------------------
   THE ONE FUNCTION EVERY PART OF THE SITE SHOULD CALL.

   Returns true on success, false on failure.
   $error is filled with a human-readable reason when it fails, so the
   admin test page can show exactly what went wrong.
------------------------------------------------------------------- */
function sendAppMail(
    $conn,
    $toEmail,
    $toName,
    $subject,
    $htmlBody,
    $textBody = '',
    &$error = null
) {
    $settings = mailSettings($conn);

    if (!$settings) {
        $error = 'The settings table is empty. Add a row in Admin > Settings first.';
        mailLog('FAIL: ' . $error);
        return false;
    }

    if (empty($settings['smtp_email'])) {
        $error = 'No sender email saved in Admin > Settings > SMTP Email.';
        mailLog('FAIL: ' . $error);
        return false;
    }

    $toEmail = trim($toEmail);

    if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Recipient email address is missing or invalid.';
        mailLog('FAIL: ' . $error . ' (got: ' . $toEmail . ')');
        return false;
    }

    if ($textBody === '') {
        $textBody = trim(strip_tags($htmlBody));
    }

    if (usingBrevoApi($settings)) {
        return sendViaBrevoAPI(
            $settings,
            $toEmail,
            $toName,
            $subject,
            $htmlBody,
            $textBody,
            $error
        );
    }

    return sendViaSMTP(
        $settings,
        $toEmail,
        $toName,
        $subject,
        $htmlBody,
        $textBody,
        $error
    );
}


/* -------------------------------------------------------------------
   TRANSPORT 1 - Brevo HTTP API over port 443.

   Works on every host, including ones that block SMTP ports.
   Uses cURL when available, and falls back to a plain HTTPS stream
   when cURL is disabled (some cheap shared hosts disable it).
------------------------------------------------------------------- */
function sendViaBrevoAPI(
    $settings,
    $toEmail,
    $toName,
    $subject,
    $htmlBody,
    $textBody,
    &$error = null
) {
    $apiKey    = trim($settings['smtp_password']);
    $fromEmail = trim($settings['smtp_email']);
    $fromName  = !empty($settings['website_name'])
        ? $settings['website_name']
        : 'Shreeji Decoration';

    $payload = json_encode([
        'sender'      => ['name' => $fromName, 'email' => $fromEmail],
        'to'          => [['email' => $toEmail, 'name' => $toName ?: $toEmail]],
        'subject'     => $subject,
        'htmlContent' => $htmlBody,
        'textContent' => $textBody,
    ]);

    $url = 'https://api.brevo.com/v3/smtp/email';

    $headers = [
        'accept: application/json',
        'content-type: application/json',
        'api-key: ' . $apiKey,
    ];

    $response       = null;
    $httpCode       = 0;
    $transportError = '';

    if (function_exists('curl_init')) {

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response       = curl_exec($ch);
        $httpCode       = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $transportError = curl_error($ch);

        curl_close($ch);

    } else {

        // cURL disabled - use a raw HTTPS stream instead
        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => implode("\r\n", $headers),
                'content'       => $payload,
                'timeout'       => 20,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if (isset($http_response_header[0])
            && preg_match('#\s(\d{3})\s#', $http_response_header[0], $m)) {
            $httpCode = (int) $m[1];
        }

        if ($response === false) {
            $transportError = 'HTTPS request failed and cURL is not installed. '
                . 'Ask your host to enable the PHP cURL extension.';
        }
    }

    mailLog("Brevo API -> to: $toEmail | HTTP $httpCode | transport: "
        . ($transportError ?: 'ok')
        . ' | response: ' . substr((string) $response, 0, 500));

    if ($transportError) {
        $error = 'Could not reach Brevo: ' . $transportError;
        return false;
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    }

    $error = brevoErrorMessage($httpCode, $response, $fromEmail);

    return false;
}


/* -------------------------------------------------------------------
   Turn Brevo's raw API error into something the shop owner can act on.
------------------------------------------------------------------- */
function brevoErrorMessage($httpCode, $response, $fromEmail)
{
    $decoded = json_decode((string) $response, true);
    $code    = isset($decoded['code'])    ? $decoded['code']    : '';
    $detail  = isset($decoded['message']) ? $decoded['message'] : (string) $response;

    if ($httpCode === 401 || $code === 'unauthorized') {
        return 'Brevo rejected the API key (HTTP 401). '
            . 'Re-copy the key from Brevo > SMTP & API > API Keys into '
            . 'Admin > Settings > SMTP Password. It must start with xkeysib- '
            . 'and have no spaces before or after it.';
    }

    if ($code === 'invalid_parameter' && stripos($detail, 'sender') !== false) {
        return 'Brevo does not recognise "' . $fromEmail . '" as a verified sender. '
            . 'Go to Brevo > Senders, Domains & Dedicated IPs > Senders, add that '
            . 'exact address, then click the confirmation link Brevo emails you.';
    }

    if ($httpCode === 402) {
        return 'Your Brevo account is out of sending credits for today '
            . '(the free plan allows 300 emails per day).';
    }

    return 'Brevo returned HTTP ' . $httpCode . ': ' . $detail;
}


/* -------------------------------------------------------------------
   TRANSPORT 2 - classic SMTP through PHPMailer.

   Tries the configured port first, then automatically retries on the
   other common port (587 <-> 465) before giving up, because a host
   sometimes blocks one but not the other.
------------------------------------------------------------------- */
function sendViaSMTP(
    $settings,
    $toEmail,
    $toName,
    $subject,
    $htmlBody,
    $textBody,
    &$error = null
) {
    $configuredPort = (int) ($settings['smtp_port'] ? $settings['smtp_port'] : 587);

    $attempts = [$configuredPort];

    $attempts[] = ($configuredPort === 465) ? 587 : 465;

    $lastError = '';

    foreach (array_unique($attempts) as $port) {

        /* Quick reachability probe first. If the host blocks this port,
           PHPMailer would sit there for its full timeout on each attempt
           and the customer stares at a spinner for half a minute. A
           4-second socket test tells us the same thing straight away. */

        $probe = @fsockopen($settings['smtp_host'], $port, $pErrNo, $pErrStr, 4);

        if (!$probe) {

            $lastError = 'Could not connect to ' . $settings['smtp_host']
                . ':' . $port . ' — ' . ($pErrStr ?: 'blocked or unreachable')
                . ' (code ' . $pErrNo . ')';

            mailLog("SMTP -> port $port unreachable: $lastError");

            continue;
        }

        fclose($probe);

        try {

            $mail = buildMailer($settings, $port);

            $mail->addAddress($toEmail, $toName ?: $toEmail);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody;

            if ($mail->send()) {
                mailLog("SMTP -> to: $toEmail | port $port | sent ok");
                return true;
            }

            $lastError = $mail->ErrorInfo;

        } catch (\Throwable $e) {

            $lastError = $e->getMessage();
        }

        mailLog("SMTP -> to: $toEmail | port $port | FAILED: $lastError");
    }

    if (stripos($lastError, 'connect') !== false
        || stripos($lastError, 'timed out') !== false
        || stripos($lastError, 'timeout') !== false) {

        $error = 'Your server cannot open an SMTP connection to '
            . $settings['smtp_host'] . ' on port ' . $configuredPort . '. '
            . 'Hosting providers block these ports by default. '
            . 'Switch to the Brevo API method (see admin/mail-test.php) - '
            . 'it sends over HTTPS and is not affected by this block. '
            . 'Raw error: ' . $lastError;

    } elseif (stripos($lastError, 'authenticate') !== false
        || stripos($lastError, '535') !== false) {

        $error = 'The mail server rejected the username or password. '
            . 'For Gmail you must use a 16-character App Password '
            . '(not the normal Gmail password) with 2-Step Verification on. '
            . 'Raw error: ' . $lastError;

    } else {

        $error = 'SMTP send failed: ' . $lastError;
    }

    return false;
}


/* -------------------------------------------------------------------
   Build a configured PHPMailer instance for a given port.
------------------------------------------------------------------- */
function buildMailer($settings, $port = null)
{
    $mail = new PHPMailer(true);

    $port = (int) ($port ? $port : ($settings['smtp_port'] ? $settings['smtp_port'] : 587));

    $mail->isSMTP();
    $mail->Host     = $settings['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_email'];
    $mail->Password = $settings['smtp_password'];
    $mail->Port     = $port;

    $mail->SMTPSecure = ($port === 465)
        ? PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer::ENCRYPTION_STARTTLS;

    // Fail fast instead of hanging until PHP's max_execution_time kills
    // the request mid-response (which is what produced the blank
    // "Unable to connect" screens).
    $mail->Timeout       = 12;
    $mail->SMTPKeepAlive = false;

    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';

    // Verbose logging only when explicitly switched on, so the log file
    // does not grow by megabytes per email in production.
    //
    // NOTE: level 3 (DEBUG_CONNECTION), not 2. This matters. At level 2
    // PHPMailer only logs the CLIENT/SERVER conversation — and when a
    // host blocks the SMTP port there IS no conversation, so a blocked
    // connection writes absolutely nothing to the log. That is exactly
    // why the old mail-debug.log showed successful localhost sends and
    // complete silence from the server. Level 3 logs the connection
    // attempt itself, so failures are visible.
    if (defined('MAIL_VERBOSE_DEBUG') && MAIL_VERBOSE_DEBUG) {
        $mail->SMTPDebug   = 3;
        $mail->Debugoutput = function ($str, $level) {
            mailLog('SMTP: ' . $str);
        };
    } else {
        $mail->SMTPDebug = 0;
    }

    $mail->setFrom(
        $settings['smtp_email'],
        $settings['website_name'] ? $settings['website_name'] : 'Shreeji Decoration'
    );

    $mail->isHTML(true);

    return $mail;
}


/* -------------------------------------------------------------------
   Kept for backwards compatibility with any older code that still
   calls getMailer($conn) directly.
------------------------------------------------------------------- */
function getMailer($conn)
{
    return buildMailer(mailSettings($conn));
}


/* -------------------------------------------------------------------
   OTP email - now a thin wrapper over sendAppMail().
------------------------------------------------------------------- */
function sendOTPEmail($conn, $customerName, $customerEmail, $otp, &$error = null)
{
    $subject = 'Your Login OTP - Shreeji Decoration';

    $htmlBody = '
    <div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;
                border:1px solid #eee;border-radius:12px;overflow:hidden;">

        <div style="background:#8B4513;color:#fff;padding:20px;text-align:center;">
            <h2 style="margin:0;">Shreeji Decoration</h2>
        </div>

        <div style="padding:30px;">

            <h3>Hello ' . htmlspecialchars($customerName) . ',</h3>

            <p>Your One-Time Password (OTP) for login is:</p>

            <div style="text-align:center;margin:30px 0;">
                <span style="font-size:36px;letter-spacing:10px;background:#FFF3E0;
                             padding:15px 30px;border-radius:10px;color:#8B4513;
                             font-weight:bold;">' . $otp . '</span>
            </div>

            <p>This OTP will expire in <b>5 minutes</b>.</p>

            <p>If you did not request this login, simply ignore this email.</p>

            <br>

            <p>Regards,<br><b>Shreeji Decoration</b></p>

        </div>
    </div>';

    $textBody = "Hello $customerName,\n\n"
        . "Your One-Time Password (OTP) for login is: $otp\n\n"
        . "This OTP will expire in 5 minutes.\n\n"
        . "If you did not request this login, simply ignore this email.\n\n"
        . "Regards,\nShreeji Decoration";

    return sendAppMail(
        $conn,
        $customerEmail,
        $customerName,
        $subject,
        $htmlBody,
        $textBody,
        $error
    );
}
