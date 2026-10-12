const popup = document.getElementById("customerLoginPopup");

function openCustomerPopup() {
    popup.classList.add("show");
}

function closeCustomerPopup() {
    popup.classList.remove("show");
}

document.getElementById("closeCustomerPopup").onclick = closeCustomerPopup;

// SEND OTP
function sendOTP() {

    let mobile = document.getElementById("customerMobile").value.trim();

    if (mobile.length !== 10) {
        alert("Enter valid mobile number");
        return;
    }

    fetch("includes/send-otp.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },

        body: "mobile=" + mobile

    })

        .then(r => r.json())

        .then(data => {

            if (data.success) {

                document.getElementById("mobileSection").style.display = "none";

                document.getElementById("otpSection").style.display = "block";

                alert("OTP Sent");

            } else {

                alert(data.message);

            }

        });

}

// VERIFY OTP
function verifyOTP() {

    let otp = document.getElementById("customerOtp").value;

    fetch("customer/login.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },

        body=
        "mobile=" + document.getElementById("customerMobile").value +
        "&otp=" + otp

    })

        .then(r => r.json())

        .then(data => {

            if (data.success) {

                /*
                -------------------------------------
                Clear previous guest cart
                -------------------------------------
                */

                localStorage.removeItem("shoppingCart");

                localStorage.removeItem("cart");

                alert("Login Successful");

                closeCustomerPopup();

                location.reload();

            }

            else {

                alert(data.message);

            }

        });

}