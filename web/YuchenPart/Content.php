<!DOCTYPE html>
<html>
<head>
    <title>Admin Login Page</title>
    <link rel="stylesheet" href="Content.css">
</head>
<body>
    <div class="square"> 
        <h1> Login <hr></h1>
        <form id="loginForm">
            <h3>Email:</h3>
            <input id="EMAIL" name="email" type="email" placeholder="Enter admin email" required>
            <span id="emailError" style="color: red;"></span>
            
            <h3>Password:</h3>
            <input id="Password" name="password" type="password" placeholder="Enter password" required>
            <span id="passwordError" style="color: red;"></span>
            <h5 id="speciala"><a href="../Devonpart/web/register.php">Don't have account yet?</a></h5>
            <h5 id="special"><a href="testing.php">Forgot password?</a></h5>
        </form>
        <button id="LOGINbutton"> Log in</button>

        <script>
            document.getElementById("LOGINbutton").addEventListener("click", function(e) {
                e.preventDefault();
                
                let Email = document.getElementById("EMAIL").value.trim();
                let Password = document.getElementById("Password").value;
                let emailError = document.getElementById("emailError");
                let passwordError = document.getElementById("passwordError");
                
                emailError.textContent = ""; 
                passwordError.textContent = "";
                
                if (Email === "") {
                    emailError.textContent = "Please fill out email";
                    return;
                }
                if (Password === "") {
                    passwordError.textContent = "Please fill out password";
                    return;
                }

                let formData = new FormData();
                formData.append("email", Email);
                formData.append("password", Password);
                
                fetch("login.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
    console.log("Server response:", data);
    if (data === "admin_success") {
        window.location.href = "Aftersignin.php";
    } else if (data === "user_success") {
        window.location.href = "http://localhost/a/Website_assignment/web/LeonPart/Code/index.php";
    } else {
        alert(data);
    }
})

                .catch(error => console.error("Error:", error));
            });
        </script>
    </div>
</body>
</html>
