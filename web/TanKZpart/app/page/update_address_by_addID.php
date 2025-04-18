<?php
include '../lib/database.php';   
session_start();

$address_id = $_POST["address_id"];
$address_name = $_POST["address_name"];
$floor_unit = $_POST["floor_unit"];
$state = $_POST["state"];
$district = $_POST["district"];
$postcode = $_POST["postcode"];

$conn = new mysqli("localhost", "root", "", "online_shopping");


if ($_POST["address_name"] == "") {
    echo "<script>document.getElementById('address_name').style.display = 'block';</script>";
    header("Location: ../lib/addressfetchbyaddressid.php?edit=$address_id;"); // 重定向到地址页面
    
    exit;
}elseif ($_POST["floor_unit"] == "") {
    echo "<script>document.getElementById('floor_unit').style.display = 'block';</script>";
    exit;
}elseif ($_POST["state"] == "") {
    echo "<script>document.getElementById('state').style.display = 'block';</script>";
    exit;
}elseif ($_POST["district"] == "") {
    echo "<script>document.getElementById('district').style.display = 'block';</script>";
    exit;
}elseif ($_POST["postcode"] == "") {
    echo "<script>document.getElementById('postcode').style.display = 'block';</script>";
    exit;
}else{
$stmt = $conn->prepare("UPDATE user_address set address_name=?,floor_unit=?,state=?,district=?,postcode=? WHERE address_id=?");
echo $address_id,$address_name ,$floor_unit,$state,$postcode,$district;
$stmt->bind_param("ssssii", $address_name, $floor_unit, $state, $district, $postcode,$address_id);

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: ../../../TanKZpart/program/address.php"); // 重定向到地址页面

exit; // 加上 exit() 确保脚本在发送头部后停止执行
}

?>