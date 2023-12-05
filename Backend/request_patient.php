<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('patient');

$patient_id = $_POST['patient_id'];
$admin_id = null; 
$status = 'pending';

// Insert the new emergency request into the emergency_requests table
$insert_request_query = $mysqli->prepare('INSERT INTO emergency_requests (patient_id, admin_id, status) VALUES (?, ?, ?)');
$insert_request_query->bind_param('iis', $patient_id, $admin_id, $status);

if ($insert_request_query->execute()) {
    $request_id = $mysqli->insert_id; // Get the generated request ID

    echo json_encode(["status" => "true", "message" => "Emergency request generated successfully", "request_id" => $request_id]);
} else {
    echo json_encode(["status" => "false", "message" => "Failed to generate emergency request"]);
}

$insert_request_query->close();
$mysqli->close();
?>