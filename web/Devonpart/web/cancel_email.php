<?php
require 'config/db.php';  

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    $email = htmlspecialchars($email);

    $stmt = $conn->prepare("DELETE FROM pending_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        echo "<h1>Registration Canceled!</h1>";
        echo "<p>Your registration request has been canceled. If this was a mistake, please sign up again.</p>";
        echo '<p><a href="register.php">Go back to the registration page</a></p>';
    } else {
        echo "<h1>Error!</h1>";
        echo "<p>There was an issue canceling your registration. Please try again later.</p>";
    }
} else {
    echo "<h1>Invalid Request!</h1>";
    echo "<p>No valid email provided. Please make sure the link is correct.</p>";
    echo '<p><a href="register.php">Go back to the registration page</a></p>';
}
?>
