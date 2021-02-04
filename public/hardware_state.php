<?php

require "conn.php";
$result = array('error' => false);
$action = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'read') {
    $id = $_GET['id'];
    $sql = $conn->query("SELECT hardware_state FROM users WHERE idUsers='$id'");
    $result = $sql->fetch_assoc();
}

if ($action == 'update') {
    $id = $_POST['id'];
    $table_state = $_POST['table_state'];
    $sql = $conn->query("UPDATE users SET hardware_state='$table_state' WHERE idUsers='$id'");
    if ($sql) {
        $result['message'] = "Saved";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to save";
    }
}

$conn->close();
echo json_encode($result);
