<?php
include '../app/lib/database.php';
include '../app/lib/query.php';
include '../app/lib/addressfetch.php';
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <link rel="stylesheet" href="../app/css/address.css">
    <script src="../app/js/address.js" defer></script>
</head>


<body>

    <div class="UpBlock"></div>
    <hr class="hr">
    <iframe name="hiddenframe" style="display: none;"></iframe>

    <div>

        <div class="LeftSideBodden">
        
            <img src="../app/image/<?php echo basename($row['picture'] ?? '../../image/default-avatar-icon-of-social-media-user-vector.jpg'); ?>" alt="Current Image" class="UserImage2">
       
            <P class="user_account2"> <?php echo mb_substr($row["user_account"] ?? "NO", 0, 10, 'UTF-8') . "...."; ?></P>
            <a href="../index.php" class="editprofile">Edit Profile</a>
            <hr style="margin-top: 40px;" color="white">
            <a href="../index.php" class="closeline">
                <img src="../app/image/importan_icon/userIcon.png" class="usericon">
                <p class="labelMyaccount">My account</p>
            </a>

            <a href="address.php" class="closeline">
                <p class="AddressLabel">Address</p>
            </a>

            <a href="security.php" class="closeline"    >
            <p class="AddressLabel">Security</p>
        </a>
        </div>


        <div class="profileblock">
            <p class="myprofile">Security</p>
        </div>

    

       
</body>