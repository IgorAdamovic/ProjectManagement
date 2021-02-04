<?php

require "conn.php";
$result = array('error' => false);
$action = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'read') {
    $sql = $conn->query("SELECT * FROM project_list");
    $project_list = array();
    while ($row = $sql->fetch_assoc()) {
        array_push($project_list, $row);
    }
    $result['project_list'] = $project_list;
}
if ($action == 'create') {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $client = $_POST['client'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];
    $contact = $_POST['contact'];
    $project_handler = $_POST['project_handler'];
    $quoted = $_POST['quoted'];
    $estimated_completion = $_POST['estimated_completion'];
    $status = $_POST['status'];
    $comments = $_POST['comments'];
    $time_stamp = date('Y-m-d H:i:s');
    $sql = $conn->query("INSERT INTO project_list (type, title, client, description, start_date, deadline, contact, project_handler, quoted, estimated_completion, status, comments, time_stamp) VALUES('$type','$title','$client','$description','$start_date','$deadline','$contact','$project_handler','$quoted','$estimated_completion','$status','$comments','$time_stamp')");
    if ($sql) {
        $result['message'] = "Project added successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to add project";
    }
}
if ($action == 'update') {
    $id = $_POST['id'];
    $type = $_POST['type'];
    $title = $_POST['title'];
    $client = $_POST['client'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $deadline = $_POST['deadline'];
    $contact = $_POST['contact'];
    $project_handler = $_POST['project_handler'];
    $quoted = $_POST['quoted'];
    $estimated_completion = $_POST['estimated_completion'];
    $status = $_POST['status'];
    $last_change = $_POST['last_change'];
    $comments = $_POST['comments'];
    $time_stamp = date('Y-m-d H:i:s');
    $sql = $conn->query("UPDATE project_list SET type='$type', title='$title', client='$client', description='$description', start_date='$start_date', deadline='$deadline', contact='$contact', project_handler='$project_handler', quoted='$quoted', estimated_completion='$estimated_completion', status='$status', last_change='$last_change', comments='$comments', time_stamp='$time_stamp' WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Project updated successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to update project";
    }
}
if ($action == 'delete') {
    $id = $_POST['id'];
    $sql = $conn->query("DELETE FROM project_list WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Project deleted successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to delete project";
    }
}

$conn->close();
echo json_encode($result);
