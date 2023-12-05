<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('admin');

$request_id = $_POST['request_id']; 
$action = $_POST['action'];  

if ($action == 'approved' || $action == 'rejected') {
    // Update the status of the emergency room request based on the action
    $update_request_query = $mysqli->prepare('UPDATE emergencyroomrequest SET status = ? WHERE request_id = ?');
    $update_request_query->bind_param('si', $action, $request_id);

    if ($update_request_query->execute()) {
        echo json_encode(["status" => "true", "message" => "Request $actioned successfully"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to $action request: " . $mysqli->error]);
    }

    $update_request_query->close();
} else {
   
    echo json_encode(["status" => "false", "message" => "Invalid action--type approved or rejected"]);
}

$mysqli->close();
?>