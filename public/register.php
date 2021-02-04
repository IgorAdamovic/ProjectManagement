<?php

require 'conn.php';
$result = array('error' => false);
$action = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'create') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordRepeat = $_POST['password2'];
    $role = $_POST['role'];

    if ($password != $passwordRepeat) {
        $result['error'] = true;
        $result['message'] = "Confirm password does not match";
    } else {
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

        $sql = $conn->query("INSERT INTO users (uidUsers, emailUsers, pwdUsers, role) VALUES ('$username', '$email', '$hashedPwd', '$role')");
        if ($sql) {
            $result['message'] = "User added successfully";
        } else {
            $result['error'] = true;
            $result['message'] = "Failed to add project";
        }
    }
}

$conn->close();
echo json_encode($result);
