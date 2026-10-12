<?php
/*
|--------------------------------------------------------------------------
| Shreeji Decoration
|--------------------------------------------------------------------------
| Shared customer Login / OTP modal.
| Include this on any root-level page (alongside includes/header.php)
| that shows the profile/login button, so the button actually works.
| Originally this markup + JS only existed inline in retail.php; it is
| now reusable so other pages don't get a "dead" login button.
|--------------------------------------------------------------------------
*/
?>
<div class="modal fade" id="customerLoginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">

    <style>
        #customerLoginModal .modal-content {
            border: none;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,.25);
        }
        #customerLoginModal .login-modal-header {
            background: linear-gradient(135deg, #B8863F, #8F6428);
            padding: 32px 30px 60px;
            text-align: center;
            position: relative;
        }
        #customerLoginModal .login-modal-icon {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: rgba(255,255,255,.18);
            border: 2px solid rgba(255,255,255,.4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 1.6rem;
            color: #fff;
        }
        #customerLoginModal .login-modal-header h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 6px;
        }
        #customerLoginModal .login-modal-header p {
            color: rgba(255,255,255,.85);
            font-size: .85rem;
            margin: 0;
        }
        #customerLoginModal .modal-body {
            padding: 0 32px 32px;
            margin-top: -32px;
        }
        #customerLoginModal .login-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
        }
        #customerLoginModal label {
            font-size: .8rem;
            font-weight: 600;
            color: #6b5a4a;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 6px;
            display: block;
        }
        #customerLoginModal .login-input-group {
            display: flex;
            align-items: center;
            border: 1.5px solid #eadfd2;
            border-radius: 12px;
            padding: 0 14px;
            transition: border-color .2s ease, box-shadow .2s ease;
            background: #FDF8F0;
        }
        #customerLoginModal .login-input-group:focus-within {
            border-color: #B8863F;
            box-shadow: 0 0 0 3px rgba(232,119,46,.12);
            background: #fff;
        }
        #customerLoginModal .login-input-group i {
            color: #8F6428;
            font-size: 1rem;
            margin-right: 10px;
        }
        #customerLoginModal .login-input-group input {
            border: none;
            background: transparent;
            padding: 12px 0;
            width: 100%;
            outline: none;
            font-size: .95rem;
        }
        #customerLoginModal .btn-send-otp,
        #customerLoginModal .btn-verify-otp {
            background: linear-gradient(135deg, #B8863F, #8F6428);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: .95rem;
            width: 100%;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        #customerLoginModal .btn-send-otp:hover,
        #customerLoginModal .btn-verify-otp:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(232,119,46,.35);
            color: #fff;
        }
        #customerLoginModal .btn-close {
            position: absolute;
            top: 18px;
            right: 18px;
            background-color: transparent;
            opacity: 1;
            filter: brightness(0) invert(1);
            box-shadow: none;
            outline: none;
        }
        #customerLoginModal .btn-close:focus,
        #customerLoginModal .btn-close:active,
        #customerLoginModal .btn-close:hover,
        #customerLoginModal .btn-close:focus-visible {
            box-shadow: none !important;
            outline: none !important;
            background-color: transparent !important;
        }
        #customerLoginModal .otp-hint {
            font-size: .8rem;
            color: #8a7862;
            margin-top: 6px;
        }
    </style>

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="login-modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="login-modal-icon">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h5>Customer Login</h5>
                <p>Sign in to track orders &amp; checkout faster</p>
            </div>

            <div class="modal-body">
                <div class="login-card">

                    <div id="loginStep">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <div class="login-input-group">
                                <i class="bi bi-person"></i>
                                <input type="text" id="customerName" placeholder="Enter your full name">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <div class="login-input-group">
                                <i class="bi bi-telephone"></i>
                                <input type="text" id="customerPhone" placeholder="10-digit mobile number">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <div class="login-input-group">
                                <i class="bi bi-envelope"></i>
                                <input type="email" id="customerEmail" placeholder="you@example.com">
                            </div>
                        </div>
                        <button class="btn-send-otp" id="sendOtpBtn">
                            <span id="sendOtpText"><i class="bi bi-send-fill me-1"></i> Send OTP</span>
                            <span id="sendOtpLoader" style="display:none;">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Sending OTP...
                            </span>
                        </button>
                    </div>

                    <div id="otpMessage"></div>

                    <div id="otpStep" style="display:none;">
                        <div class="mb-3">
                            <label>Enter OTP</label>
                            <div class="login-input-group">
                                <i class="bi bi-shield-lock"></i>
                                <input type="text" maxlength="6" id="customerOtp" placeholder="6-digit code">
                            </div>
                            <p class="otp-hint">Check your email for the verification code</p>
                        </div>
                        <button class="btn-verify-otp" id="verifyOtpBtn">
                            <span id="verifyOtpText"><i class="bi bi-check2-circle me-1"></i> Verify OTP</span>
                            <span id="verifyOtpLoader" style="display:none;">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Verifying...
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var sendBtn = document.getElementById("sendOtpBtn");
    if (sendBtn) {
        sendBtn.addEventListener("click", function() {

            let name = document.getElementById("customerName").value.trim();
            let phone = document.getElementById("customerPhone").value.trim();
            let email = document.getElementById("customerEmail").value.trim();

            if (name == "" || phone == "" || email == "") {
                alert("Please fill all fields.");
                return;
            }

            let formData = new FormData();
            formData.append("name", name);
            formData.append("phone", phone);
            formData.append("email", email);

            document.getElementById("sendOtpBtn").disabled = true;
            document.getElementById("sendOtpText").style.display = "none";
            document.getElementById("sendOtpLoader").style.display = "inline";

            fetch("customer/send-otp.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("sendOtpBtn").disabled = false;
                    document.getElementById("sendOtpText").style.display = "inline";
                    document.getElementById("sendOtpLoader").style.display = "none";

                    if (data.success) {
                        document.getElementById("loginStep").style.display = "none";
                        document.getElementById("otpStep").style.display = "block";
                        document.getElementById("otpMessage").innerHTML = `
<div class="alert alert-success">
<i class="bi bi-check-circle-fill me-2"></i>
OTP has been sent successfully to
<b>${email}</b>
</div>
`;
                    } else {
                        alert(data.message || "Unable to send OTP.");
                    }
                })
                .catch(error => {
                    document.getElementById("sendOtpBtn").disabled = false;
                    document.getElementById("sendOtpText").style.display = "inline";
                    document.getElementById("sendOtpLoader").style.display = "none";
                    console.error(error);
                    alert("Unable to connect.");
                });
        });
    }

    var verifyBtn = document.getElementById("verifyOtpBtn");
    if (verifyBtn) {
        verifyBtn.addEventListener("click", function() {

            let otp = document.getElementById("customerOtp").value.trim();
            let email = document.getElementById("customerEmail").value.trim();

            if (otp == "") {
                alert("Please enter OTP");
                return;
            }

            document.getElementById("verifyOtpBtn").disabled = true;
            document.getElementById("verifyOtpText").style.display = "none";
            document.getElementById("verifyOtpLoader").style.display = "inline";

            let formData = new FormData();
            formData.append("email", email);
            formData.append("otp", otp);

            fetch("customer/verify-otp.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("verifyOtpBtn").disabled = false;
                    document.getElementById("verifyOtpText").style.display = "inline";
                    document.getElementById("verifyOtpLoader").style.display = "none";

                    if (data.success) {
                        document.getElementById("otpMessage").innerHTML = `
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            Login Successful
            <br>
            Redirecting...
        </div>
        `;
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    document.getElementById("verifyOtpBtn").disabled = false;
                    document.getElementById("verifyOtpText").style.display = "inline";
                    document.getElementById("verifyOtpLoader").style.display = "none";
                    console.log(error);
                    alert("Verification Failed");
                });
        });
    }

    var loginButton = document.getElementById("openLoginModal");
    if (loginButton) {
        loginButton.addEventListener("click", function() {
            let modal = new bootstrap.Modal(document.getElementById("customerLoginModal"));
            modal.show();
        });
    }
})();
</script>