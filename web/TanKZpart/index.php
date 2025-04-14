<?php
include 'app/lib/database.php';
session_start();
$_SESSION['user_id'] = 1;
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



    <div class="LeftSideBodden">

        <img src="app/image/<?php echo basename($row['picture']); ?>" alt="Current Image" class="UserImage2">
        <img src="app/image/importan_icon/<?php echo (!empty($row['picture']) && file_exists("app/image/" . $row['picture'])) ? basename($row['picture']) : 'importan_icon/userIcon.png'; ?>" alt="Current Image" class="UserImage2">
        <P class="user_account2"> <?php echo mb_substr($row["user_account"] ?? "NO", 0, 10, 'UTF-8') . "...."; ?></P>
        <a href="index.php" class="editprofile">Edit Profile</a>
        <hr style="margin-top: 40px;" color="white">
        <a href="index.php" class="closeline">
            <img src="app/image/importan_icon/userIcon.png" class="usericon">
            <p class="labelMyaccount">My account</p>
        </a>

        <a href="program/address.php" class="closeline">
            <p class="AddressLabel">Address</p>
        </a>

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
                <img src="app/image/<?php echo basename($row['picture']); ?>" alt="Current Image" class="UserImage">
                <input type="file" accept="image/*" class="imgaccept" name="picture" id="imageupload">

            </div>
        </form>


        <div class="moveDown">
            <form method="post" action="app/page/updateUser.php" target="hiddenframe">
                <label for="user_Account_label" class="userAccountLabel">User Account</label>
                <input type="text" id="user_Account_label" class="userAccountTextLabel" name="user_account" value="<?php echo $row["user_account"] ?? "NO" ?>">

                <p class="userAccountNotices">User Account can only be changed once.</p>


                <div class="moveDown">

                    <label for="user_Name_Label" class="userNameLabel">Name</label>
                    <input type="text" id="user_Name_Label" class="userNameTextLabel" name="username" value="<?php echo $row["username"] ?? "NO"  ?>">

                </div>

                <div class="moveDown">

                    <label class="EmailLabel">E-mail</label>
                    <div class="EmailTextLabel"><?php echo $row["email"] ?? ""  ?></div>
                    <input type="button" value="change" id="emailSubmit" class="emailSubmit">

                </div>

                <div class="moveDown">

                    <label class="PhoneNumberLabel">Phone Number</label>
                    <div class="PhoneNumberTextLabel"> <?php echo $row["phone_number"] ?? ""  ?> </div>
                    <input type="button" value="change" id="PhoneNumberSubmit" class="PhoneNumberSubmit">
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