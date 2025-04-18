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
    header("Location: ../lib/addressfetchbyaddressid.php?edit=$address_id&error=name_address");
    exit;
} elseif ($_POST["floor_unit"] == "") {
    header("Location: ../lib/addressfetchbyaddressid.php?edit=$address_id&error=floor_unit2");
    exit;
} elseif ($_POST["state"] == "") {
    header("Location: ../lib/addressfetchbyaddressid.php?edit=$address_id&error=state2");
    exit;
} elseif ($_POST["district"] == "") {
    header("Location: ../lib/addressfetchbyaddressid.php?edit=$address_id&error=district2");
    exit;
} elseif ($_POST["postcode"] == "") {
    header("Location: ../lib/addressfetchbyaddressid.php?edit=$address_id&error=postcode2");
    exit;
} else {
    $stmt = $conn->prepare("UPDATE user_address set address_name=?,floor_unit=?,state=?,district=?,postcode=? WHERE address_id=?");
    $stmt->bind_param("ssssii", $address_name, $floor_unit, $state, $district, $postcode, $address_id);

    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../../../TanKZpart/program/address.php"); // 重定向到地址页面

    exit; // 加上 exit() 确保脚本在发送头部后停止执行
}
