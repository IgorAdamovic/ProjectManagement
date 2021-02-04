<?php

require "conn.php";
$result = array('error' => false);
$action = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'read') {
    $sql = $conn->query("SELECT * FROM updates");
    $update_list = array();
    while ($row = $sql->fetch_assoc()) {
        array_push($update_list, $row);
    }
    $result['update_list'] = $update_list;
}
if ($action == 'create') {
    $development = $_POST['development'];
    $description = $_POST['description'];
    $date_entered = $_POST['date_entered'];
    $level_of_urgency = $_POST['level_of_urgency'];
    $estimated_completion = $_POST['estimated_completion'];
    $status = $_POST['status'];
    $comments = $_POST['comments'];
    $time_stamp = date('Y-m-d H:i:s');
    $solutions = $_POST['solutions'];
    $sql = $conn->query("INSERT INTO updates (development, description, date_entered, level_of_urgency, estimated_completion, status, comments, time_stamp, solutions) VALUES('$development','$description','$date_entered','$level_of_urgency','$estimated_completion','$status','$comments', '$time_stamp', '$solutions')");
    if ($sql) {
        $result['message'] = "Update added successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to add update";
    }
}
if ($action == 'update') {
    $id = $_POST['id'];
    $development = $_POST['development'];
    $description = $_POST['description'];
    $date_entered = $_POST['date_entered'];
    $level_of_urgency = $_POST['level_of_urgency'];
    $estimated_completion = $_POST['estimated_completion'];
    $status = $_POST['status'];
    $last_change = $_POST['last_change'];
    $comments = $_POST['comments'];
    $time_stamp = date('Y-m-d H:i:s');
    $solutions = $_POST['solutions'];
    $sql = $conn->query("UPDATE updates SET development='$development', description='$description', date_entered='$date_entered', level_of_urgency='$level_of_urgency', estimated_completion='$estimated_completion', status='$status', last_change='$last_change', comments='$comments', time_stamp='$time_stamp', solutions='$solutions' WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Update updated successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to update update";
    }
}
if ($action == 'delete') {
    $id = $_POST['id'];
    $sql = $conn->query("DELETE FROM updates WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Update deleted successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to delete update";
    }
}

$conn->close();
echo json_encode($result);
