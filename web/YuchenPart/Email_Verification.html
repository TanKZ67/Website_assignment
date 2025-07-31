<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email and Change Password</title>
    <script src="https://cdn.emailjs.com/dist/email.min.js"></script>
    <link rel="stylesheet" href="Email_verification.css">
</head>
<body>
    <div>
        <h2>Send Email</h2>
        <form id="emailForm">
            <label for="email">Email address</label>
            <input type="email" id="email" name="to_email" required>
            <input type="hidden" name="random_number" id="randomNumber">
            <button type="submit" id="sendEmailBtn">Send Email</button>
            <span id="timerText"></span> <!-- Countdown display -->
        </form>

        <h2>Change Password</h2>
        <form id="pincodeForm">
            <h3>Enter the pincode to change your password.</h3>
            <input type="password" id="inputpincode" placeholder="Enter pincode here">
            <span id="Errorpincode"></span>
            <button id="submitpincode" type="button">SUBMIT</button>
        </form>
    </div>

    <script>
        // Initialize EmailJS
        (function(){
            emailjs.init("WNuxY682Ux6oPiCUN");  // Your EmailJS Public Key
        })();

        let sentRandomNumber = null; // Store the sent OTP
        let countdownTimer = null; // Countdown timer variable
        let countdown = 40; // 40-second countdown
        let sendAttempt = 0; // Number of email send attempts
        const maxAttempts = 3; // Maximum of 3 sends allowed
        const timerText = document.getElementById("timerText");
        const sendEmailBtn = document.getElementById("sendEmailBtn");

        // Generate a random 4-digit OTP
        function generateRandomNumber() {
            return Math.floor(1000 + Math.random() * 9000);
        }

        // Handle email sending
        document.getElementById("emailForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission
            
            if (sendAttempt >= maxAttempts) {
                alert("You have reached the maximum number of OTP requests and will be redirected to the home page!");
                window.location.href = "Content.php"; // Redirect to Content.php
                return;
            }

            if (countdown > 0 && sentRandomNumber !== null) {
                alert(`Please wait ${countdown} seconds before sending the email again!`);
                return;
            }

            sendOTP();
        });

        // Send the OTP and start the countdown
        function sendOTP() {
            const randomNum = generateRandomNumber();
            sentRandomNumber = randomNum.toString();
            document.getElementById("randomNumber").value = randomNum;
            sendAttempt++; // Increment send attempts

            emailjs.sendForm("service_9q3qmh4", "template_g61ng7d", document.getElementById("emailForm"))
                .then(response => {
                    console.log("Success:", response);
                    startCountdown();
                })
                .catch(error => {
                    alert("Failed to send email!");
                    console.error("Error:", error);
                    sentRandomNumber = null;
                });

            // Check if max attempts reached
            if (sendAttempt >= maxAttempts) {
                alert("You have reached the maximum OTP sending limit. Redirecting to the homepage!");
                setTimeout(() => window.location.href = "Content.php", 3000); // Redirect after 3 seconds
            }
        }

        // Start the countdown
        function startCountdown() {
            clearInterval(countdownTimer); // Clear existing timer
            countdown = 40;
            sendEmailBtn.disabled = true; // Disable the send button
            timerText.textContent = `OTP valid for: ${countdown} seconds`;

            countdownTimer = setInterval(() => {
                countdown--;
                timerText.textContent = `OTP valid for: ${countdown} seconds`;

                if (countdown <= 0) {
                    clearInterval(countdownTimer);
                    sentRandomNumber = null;
                    timerText.textContent = "OTP has expired. Please send the email again!";
                    sendEmailBtn.disabled = false; // Re-enable the send button
                }
            }, 1000);
        }

        // Handle pincode input
        document.getElementById("submitpincode").addEventListener("click", function(event) {
            event.preventDefault();
            let userpincode = document.getElementById("inputpincode").value.trim();
            let pincodeError = document.getElementById("Errorpincode");

            pincodeError.textContent = ""; // Clear error message

            if (userpincode === "") {
                pincodeError.textContent = "Please enter the pincode";
            } else if (sentRandomNumber === null) {
                pincodeError.textContent = "OTP has expired, please resend the email";
            } else if (userpincode === sentRandomNumber) {
                clearInterval(countdownTimer); // Stop the countdown
                window.location.href = "reset_password.php";
            } else {
                pincodeError.textContent = "Incorrect pincode, please try again";
                document.getElementById("inputpincode").value = "";
            }
        });
    </script>
</body>
</html>
