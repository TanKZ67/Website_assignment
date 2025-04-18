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

            <img src="../app/image/<?php echo basename($row['picture']); ?>" alt="Current Image" class="UserImage2">
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

        </div>


        <div class="profileblock">
            <p class="myprofile">My Address</p>
            <p class="manageP">Manage your address to ensure your product will be send</p>
            <div class="movedown"></div>


            <button class="newaddress" id="newaddres">+ Add New Address</button>
            <div class="address-box">



                <tbody id="product-list">
                    <?php
                    $conn = mysqli_connect('localhost', 'root', '', 'online_shopping');
                    $user_id = $_SESSION['user_id'];
                    $select = mysqli_query($conn, "SELECT * FROM user_address WHERE user_id = $user_id");
                    while ($addrow = mysqli_fetch_assoc($select)) { ?>
                        <tr>
                            <td>
                            <a href="../app/lib/addressfetchbyaddressid.php?edit=<?php echo $addrow['address_id']; ?>" class="edit" id="edit"> Edit</a>
                            </td>
                            <td>
                                <p class="addname"><?php echo $addrow["address_name"]; ?></p>
                            </td>
                            <td>
                                <p class="floor"><?php echo $addrow["floor_unit"]; ?>,<?php echo $addrow["state"]; ?> </p>
                            </td>
                            <td>
                                <p class="district"><?php echo $addrow["district"]; ?>,<?php echo $addrow["postcode"]; ?></p>
                            </td>
                            <hr>
                        </tr>
                    <?php } ?>
                </tbody>
                


            </div>
        </div>

        <div id="layerNA1" class="layerNA11">
            <div class="layerNA22">
                <div class="layertext">Add New Address</div>
                <hr>
                <div class="layerNA3">
                    <form action="../app/page/address.php" method="post" target="hiddenframe">
                        <input type="text" placeholder="Name" name="address_name" class="inputaddname" require>
                        <input type="text" placeholder="Postcode" name="postcode" class="inputaddpost" require>
                        <input type="text" placeholder="House Number,Building,Street Name" name="floor_unit" class="inputaddfloor" require>
                        <input type="text" placeholder="State" name="state" class="inputaddstate" require>
                        <input type="text" placeholder="District" name="district" class="inputaddsid" require>
                        <input type="submit" id="savebutton" class="buttonsavenewadd" value="save">
                        <button type="button" id="cancelbutton2" class="cancelbutton">cancel</button>
                    </form>
                </div>
            </div>
        </div>

       
</body>