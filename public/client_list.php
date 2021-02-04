<?php

require "conn.php";

$result = array('error'=>false);
$action = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'read') {
    $sql = $conn->query("SELECT * FROM client_list");
    $client_list = array();
    while ($row = $sql->fetch_assoc()) {
       array_push($client_list, $row);
    }
    $result['client_list'] = $client_list;
}
if ($action == 'create') {
    $client = $_POST['client'];
    $contact_details = $_POST['contact_details'];
    $invoice_contact = $_POST['invoice_contact'];
    $address = $_POST['address']; 
    $sql = $conn->query("INSERT INTO client_list (client, contact_details, invoice_contact, address) VALUES('$client','$contact_details','$invoice_contact','$address')");
    if ($sql) {
        $result['message'] = "Client added successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to add client";
    }
}
if ($action == 'update') {
    $id = $_POST['id'];
    $client = $_POST['client'];
    $contact_details = $_POST['contact_details'];
    $invoice_contact = $_POST['invoice_contact'];
    $address = $_POST['address'];
    $sql = $conn->query("UPDATE client_list SET client='$client', contact_details='$contact_details', invoice_contact='$invoice_contact', address='$address' WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Client updated successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to update client";
    }
}
if ($action == 'delete') {
    $id = $_POST['id'];
    $sql = $conn->query("DELETE FROM client_list WHERE id='$id'");
    if ($sql) {
        $result['message'] = "Client deleted successfully";
    } else {
        $result['error'] = true;
        $result['message'] = "Failed to delete client";
    }
}

$conn->close();
echo json_encode($result);
?>