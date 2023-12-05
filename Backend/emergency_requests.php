<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('admin');

$admin_id = $_POST['admin_id'];
$request_id = $_POST['request_id'];
$status = $_POST['status']; // Should be 'approved', 'denied', or 'pending'

// Check if the request exists
$check_request_query = $mysqli->prepare('SELECT * FROM emergencyroomrequests WHERE request_id = ?');
$check_request_query->bind_param('i', $request_id);
$check_request_query->execute();
$request = $check_request_query->get_result()->fetch_assoc();
$check_request_query->close();

if ($request) {
    // Request exists, update the status
    $update_request_query = $mysqli->prepare('UPDATE emergencyroomrequests SET status = ?, admin_id = ? WHERE request_id = ?');
    $update_request_query->bind_param('sii', $status, $admin_id, $request_id);

    if ($update_request_query->execute()) {
        echo json_encode(["status" => "true", "message" => "Emergency request updated successfully"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to update emergency request"]);
    }

    $update_request_query->close();
} else {
    echo json_encode(["status" => "false", "message" => "Emergency request not found"]);
}

$mysqli->close();
?>
