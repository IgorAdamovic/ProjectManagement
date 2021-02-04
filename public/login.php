<?php

require 'conn.php';
$result = array('error' => false);
$action = '';

$mailuid = $_POST['mailuid'];
$password = $_POST['password'];

$sql = $conn->query("SELECT * FROM users WHERE uidUsers='$mailuid' OR emailUsers='$mailuid';");

if ($row = $sql->fetch_assoc()) {
    $pswCheck = password_verify($password, $row['pwdUsers']);
    if ($pswCheck == false) {
        $result['error'] = true;
        $result['message'] = "Wrong password";
    } else {
        $result['idUsers'] = $row['idUsers'];
        $result['uidUsers'] = $row['uidUsers'];
        $result['pwdUsers'] = $row['pwdUsers'];
        $result['role'] = $row['role'];
    }
} else {
    $result['error'] = true;
    $result['message'] = "User doesn't exist";
}

$conn->close();
echo json_encode($result);
