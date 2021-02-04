<?php

require "conn.php";
$result = array('error' => false);
$action = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'read') {
    $sql = $conn->query("SELECT * FROM hardware_list");
    $hardware_list = array();
    while ($row = $sql->fetch_assoc()) {
        array_push($hardware_list, $row);
    }
    $result['hardware_list'] = $hardware_list;
}
if ($action == 'create') {
    $development_name = $_POST['development_name'];
    $client = $_POST['client'];
    $hardware_installed = $_POST['hardware_installed'];
    $development_address = $_POST['development_address'];
    $contact = $_POST['contact'];
    $date_installed = $_POST['date_installed'];
    $extra_info = $_POST['extra_info'];
    $comments = $_POST['comments'];
    $time_stamp = date('Y-m-d H:i:s');
    $sql = $conn->query("INSERT INTO hardware_list (development_name, client, hardware_installed, development_address, contact, date_installed, extra_info, comments, time_stamp) VALUES('$development_name','$client','$hardware_installed','$development_address','$contact','$date_installed','$extra_info','$comments','$time_stamp')");
    if ($sql) {
        $result['message'] = "Hardware added successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to add hardware";
    }
}
if ($action == 'update') {
    $id = $_POST['id'];
    $development_name = $_POST['development_name'];
    $client = $_POST['client'];
    $hardware_installed = $_POST['hardware_installed'];
    $development_address = $_POST['development_address'];
    $contact = $_POST['contact'];
    $date_installed = $_POST['date_installed'];
    $extra_info = $_POST['extra_info'];
    $last_change = $_POST['last_change'];
    $comments = $_POST['comments'];
    $time_stamp = date('Y-m-d H:i:s');
    $sql = $conn->query("UPDATE hardware_list SET development_name='$development_name', client='$client', hardware_installed='$hardware_installed', development_address='$development_address', contact='$contact', date_installed='$date_installed', extra_info='$extra_info', last_change='$last_change', comments='$comments', time_stamp='$time_stamp' WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Hardware updated successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to update hardware";
    }
}
if ($action == 'delete') {
    $id = $_POST['id'];
    $sql = $conn->query("DELETE FROM hardware_list WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Hardware deleted successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to delete hardware";
    }
}

$conn->close();
echo json_encode($result);
