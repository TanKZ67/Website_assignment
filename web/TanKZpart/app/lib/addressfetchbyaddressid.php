<?php
$conn = new mysqli("localhost", "root", "", "online_shopping");

if ($conn->connect_error) {
    die("连接失败：" . $conn->connect_error);
}

// 确保 $_GET['edit'] 存在，并且是一个数字
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $address_id = $_GET['edit'];

    // 使用 prepare 预处理语句
    $stmt = $conn->prepare("SELECT * FROM user_address WHERE address_id = ?");
    $stmt->bind_param("i", $address_id); // "i" 表示整数类型

    $stmt->execute();

    $result = $stmt->get_result(); // 获取结果集

    // 如果有结果，则提取，否则返回默认值
    $addressrows = $result->fetch_assoc() ?: [
        "address_id" => "",
        "address_name" => "",
        "postcode" => "",
        "floor_unit" => "",
        "state" => "",
        "district" => "",
    ];

    $stmt->close(); // 可选但推荐
} else {
    echo "edit 参数未设置或无效";
    exit;
}
?>



<?php session_start();
include 'database.php';
include 'query.php';
include 'addressfetch.php';

?>
<!DOCTYPE html>

<html lang="en">

<head>
    <link rel="stylesheet" href="../css/address.css">
    <script src="../js/addressfectbyaddressid.js" defer></script>
</head>

<body>

    <div class="UpBlock"></div>
    <hr class="hr">
    <iframe name="hiddenframe" style="display: none;"></iframe>

    <div>

        <div class="LeftSideBodden">

            <img src="../image/<?php echo basename($row['picture']); ?>" alt="Current Image" class="UserImage2">
            <P class="user_account2"> <?php echo mb_substr($row["user_account"] ?? "NO", 0, 10, 'UTF-8') . "...."; ?></P>
            <a href="../index.php" class="editprofile">Edit Profile</a>
            <hr style="margin-top: 40px;" color="white">
            <a href="../index.php" class="closeline">
                <img src="../image/importan_icon/userIcon.png" class="usericon">
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
                                <a href="../lib/addressfetchbyaddressid.php?edit=<?php echo $addrow['address_id']; ?>" class="edit" id="edit"> Edit</a>
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

        <div id="layerA12" class="layerA12">
            <div class="layerA22">
                <div class="layertext">Add New Address</div>
                <hr>
                <div class="layerA3">
                    <form action="../page/update_address_by_addID.php" method="post">
                        <input type="hidden" name="address_id" value="<?= $addressrows['address_id'] ?? ''; ?>">
                        <input type="text" placeholder="Name" name="address_name" class="inputaddname" value="<?= $addressrows['address_name'] ?? ''; ?>" >
                        <input type="text" placeholder="Postcode" name="postcode" class="inputaddpost" value="<?= $addressrows['postcode'] ?? ''; ?>" >
                        <input type="text" placeholder="House Number,Building,Street Name" name="floor_unit" class="inputaddfloor" value="<?= $addressrows['floor_unit'] ?? ''; ?>" >
                        <input type="text" placeholder="State" name="state" class="inputaddstate" value="<?= $addressrows['state'] ?? ''; ?>" >
                        <input type="text" placeholder="District" name="district" class="inputaddsid" value="<?= $addressrows['district'] ?? ''; ?>" >
                        <input type="submit" id="savebuttonbyADDid" class="buttonsavenewadd" value="save">
                        <button type="button" class="cancelbutton" onclick="window.location.href='/a/Website_assignment/web/TanKZpart/program/address.php'">cancel</button>
                      
                    </form>
                </div>
            </div>
        </div>

                        <div id="error" class="error">
                            <div >
                                <p class="name_address" id="name_address">address name is empty</p>
                            </div>
                            <div >
                                <p class="floor_unit" id="floor_unit">floor_unit is empty</p>
                            </div>
                            <div >
                                <p class="state" id="state">state is empty</p>
                            </div>
                            <div >
                                <p class="district" id="district">district is empty</p>
                            </div>
                            <div >
                                <p class="postcode" id="postcode">postcode is empty</p>
                            </div>
                        </div>

        
</body>