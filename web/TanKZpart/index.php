<?php
session_start();
include 'app/lib/database.php';
include 'app/lib/query.php';

?>
<!DOCTYPE html>
<html lang="en">
<div class="UpBlock"></div>

<head>
    <link rel="stylesheet" href="app/css/app.css">
    <script src="app/js/index.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>

</head>

<body>

    <iframe name="hiddenframe" style="display: none;"></iframe>
    <a href="../LeonPart/Code/index.php">
    <img src="app/image/importan_icon/giphy.gif" class="img">
    </a>
    <div class="LeftSideBodden">

        <img src="../Devonpart/web/uploads/<?php echo basename($row['picture'] ?? '../image/default-avatar-icon-of-social-media-user-vector.jpg'); ?>" alt="Current Image" class="UserImage2">
        <P class="user_account2"> <?php echo mb_substr($row["user_account"] ?? "Unknow", 0, 10, 'UTF-8') . "......"; ?></P>
        <a href="index.php" class="editprofile">Edit Profile</a>
        <hr style="margin-top: 40px;" color="white">
        <a href="index.php" class="closeline">
            <img src="app/image/importan_icon/userIcon.png" class="usericon">
            <p class="labelMyaccount">My account</p>
        </a>

        <a href="program/address.php" class="closeline">
            <p class="AddressLabel">Address</p>
        </a>

        <p onclick="toggleOptions()" class="security">Security</p>

        <div id="options">
            <a href="../Devonpart/web/forgot_password.php" style="color:grey; display: inline-block; text-decoration: none;">reset password</a>
            <a href="app/page/logout.php" style="margin-top: 10px; display: inline-block; color:grey;text-decoration: none;">logout</a>
        </div>

    </div>


    <div class="profileblock">

        <p class="myprofile">My profile</p>
        <p class="manageP">Manage your profile to control and secure your account</p>

        <hr>
        <span class="vertical-line"></span>
        <?php




        ?>
        <form method="post" action="app/page/image.php" enctype="multipart/form-data" id="uploadimg" target="hiddenframe">
            <div class="imageUpdateBoss" id="imageUpdate">
            <img src="../Devonpart/web/uploads/<?php echo basename($row['picture'] ?? '../image/default-avatar-icon-of-social-media-user-vector.jpg'); ?>" alt="Current Image" class="UserImage">
                <input type="file" accept="image/*" class="imgaccept" name="picture" id="imageupload">
            </div>
        </form>


        <div class="moveDown">
            <form method="post" action="app/page/updateUser.php" target="hiddenframe">
                <?php
                echo '<label for="user_Account_label" class="userAccountLabel">User Account</label>';

                if ($row["user_account_check"] == 0) {
                    echo '<input type="text" id="user_Account_label" class="userAccountTextLabel" name="user_account" value="' . ($row["user_account"] ?? '') . '">';
                    echo '<p class="userAccountNotices">You can change your User Account once.</p>';
                } else {
                    echo '<input type="text" id="user_Account_label" class="userAccountTextLabel" name="user_account" value="' . ($row["user_account"] ?? '') . '" readonly>';
                    echo '<p class="userAccountNotices">You have already changed it and you can\'t change it agian.</p>';
                }
                ?>


                <div class="moveDown">
                    <label class="EmailLabel">E-mail</label>
                    <div class="EmailTextLabel"><?php echo $row["email"] ?? ""  ?></div>
                    <?php if (empty($row["email"])) echo ' <input type="button" value="add" id="emailSubmit" class="emailSubmit2">';
                    else  echo '<input type="button" value="change" id="emailSubmit" class="emailSubmit">'; ?>
                </div>

                <div class="moveDown">

                    <label class="PhoneNumberLabel">Phone Number</label>
                    <div class="PhoneNumberTextLabel"> <?php echo $row["phone_number"] ?? ""  ?> </div>
                    <?php if (empty($row["phone_number"])) echo ' <input type="button" value="add" id="PhoneNumberSubmit">';
                    else  echo '<input type="button" value="change" id="PhoneNumberSubmit" class="PhoneNumberSubmit">'; ?>

                </div>


                <div class="moveDown">


                    <label class="GenderLabel"> Gender </label>
                    <input type="radio" id="genderM" name="gender" value="M"
                        <?php echo ($row["gender"] ?? "") === "M" ? "checked" : ""; ?>>
                    <label for="genderM">Male</label>

                    <input type="radio" id="genderF" name="gender" value="F"
                        <?php echo ($row["gender"] ?? "") === "F" ? "checked" : ""; ?>>
                    <label for="genderF">Female</label>

                </div>

                <div class="moveDown">

                    <label for="Date_Of_Birth_Label" class="DateOfBirth">Date of birth</label>
                    <input type="date" id="Date_Of_Birth_Label" class="DateOfBirthLabel" name="date_of_birth" value="<?php echo $row["date_of_birth"] ?? "" ?>">
                </div>

                <div class="moveDown"></div>
                <input type="submit" value="save" class="save_button" id="savebutton">

            </form>

        </div>
    </div>
    <div id="layerEM1" class="layerEM11">
        <div id="layerEM2" class="layerEM22">
            <div class="layertext">Change Email Address</div>
            <hr>
            <form action="app/page/saveEmail.php" method="post" target="hiddenframe">
                <label>New email address</label>
                <input type="text" value="<?php echo $row["email"] ?? "" ?>" name="email" id="emailInput" required>
                <button type="submit" id="comfirmYesEmail" class="buttonA">Next</button>
                <button type="button" id="comfirmNoEmail" class="buttonB">Cancel</button>
            </form>
            <div class="emailOTP" id="emailOTP">
                <label for="emailOTP" class="emailOTPLabel">OTP</label>
                <input type="text" class="emailOTPtext" id="OTP">
                <button type="button" class="OTPverify" id="OTPbutton">verify</button>
            </div>

        </div>
    </div>



    <div id="layerPN1" class="layerPN11">
        <div id="layerPN2" class="layerPN22">
            <div class="layertext">Edit Phone Number</div>
            <hr>
            <form action="app/page/phone_number.php" method="post" target="hiddenframe">
                <label>New Phone Number</label>
                <input type="text" value="<?php echo $row["phone_number"] ?? "" ?>" name="phone_number" id="phonenumberInput" required>
                <button type="submit" id="comfirmYesPN" class="buttonA">Next</button>
                <button type="button" id="comfirmNoPN" class="buttonB">Cancel</button>
            </form>
        </div>
    </div>

</body>

<script>
  function toggleOptions() {
    const opt = document.getElementById("options");
    opt.classList.toggle("show");
  }
</script>