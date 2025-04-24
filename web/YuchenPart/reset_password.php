<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset_password.css">
</head>
<body>
    <div class="container">
        <h2>Change password</h2>
        <form id="resetPasswordForm">
            <div class="form-group">
                <label for="email">Email：</label>
                <input type="email" id="email" name="email" required>
                <span id="usernameError" class="error"></span>
            </div>
            <div class="form-group">
                <label for="newPassword">New password：</label>
                <input type="password" id="newPassword" name="newPassword" required>
                <span id="newPasswordError" class="error"></span>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm new password：</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <span id="confirmPasswordError" class="error"></span>
            </div>
            <button type="submit">Submit change</button>
        </form>
    </div>

    <script>
        function validatePassword(password) {
    const minLength = 3;
    const maxLength = 10;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasSpecialChar = /[^a-zA-Z0-9]/.test(password); // 任意非字母数字字符

    if (password.length < minLength || password.length > maxLength) {
        return "Password must be between 3 and 10 characters long";
    }
    if (!hasUpperCase) {
        return "Password must include at least one uppercase letter";
    }
    if (!hasLowerCase) {
        return "Password must include at least one lowercase letter";
    }
    if (!hasSpecialChar) {
        return "Password must include at least one special character";
    }
    return ""; // 验证通过
}


        document.getElementById("resetPasswordForm").addEventListener("submit", function(e) {
            e.preventDefault();

            let email = document.getElementById("email").value.trim();
            let newPassword = document.getElementById("newPassword").value.trim();
            let confirmPassword = document.getElementById("confirmPassword").value.trim();
            let usernameError = document.getElementById("usernameError");
            let newPasswordError = document.getElementById("newPasswordError");
            let confirmPasswordError = document.getElementById("confirmPasswordError");

            usernameError.textContent = "";
            newPasswordError.textContent = "";
            confirmPasswordError.textContent = "";

            if (email === "") {
                usernameError.textContent = "Please enter email";
                return;
            }

            if (newPassword === "") {
                newPasswordError.textContent = "Please enter password";
                return;
            }

            const passwordValidationMessage = validatePassword(newPassword);
            if (passwordValidationMessage) {
                newPasswordError.textContent = passwordValidationMessage;
                return;
            }

            if (confirmPassword === "") {
                confirmPasswordError.textContent = "Please confirm password";
                return;
            }

            if (newPassword !== confirmPassword) {
                confirmPasswordError.textContent = "Passwords do not match";
                return;
            }

            let formData = new FormData();
            formData.append("action", "reset_password");
            formData.append("email", email);
            formData.append("new_password", newPassword);

            fetch("login.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === "success") {
                    alert("Password changed successfully");
                    window.location.href = "Content.php";
                } else {
                    alert(data);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    </script>
</body>
</html>
