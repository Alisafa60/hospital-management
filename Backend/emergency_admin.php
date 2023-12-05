<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('admin');

$request_id = $_POST['request_id'];
$action = $_POST['action'];

if ($action == 'approved') {
    // trieve user_id retrieval from the emergency room request to set it later to a room
    $select_request_query = $mysqli->prepare('SELECT user_id FROM emergencyroomrequest WHERE request_id = ?');
    $select_request_query->bind_param('i', $request_id);
    $select_request_query->execute();
    $select_request_query->bind_result($user_id);

    if ($select_request_query->fetch()) {
        $select_request_query->close()
        $update_request_query = $mysqli->prepare('UPDATE emergencyroomrequest SET status = ? WHERE request_id = ?');
        $update_request_query->bind_param('si', $action, $request_id);

        if ($update_request_query->execute()) {
            // find first available room
            $update_room_query = $mysqli->prepare('UPDATE rooms SET availability = "occupied", user_id = ? WHERE availability = "free" LIMIT 1');
            $update_room_query->bind_param('i', $user_id);
            $update_room_query->execute();
            echo json_encode(["status" => "true", "message" => "Request approved, and room marked as occupied"]);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to approve request: " . $mysqli->error]);
        }

        $update_request_query->close();
    } else {
       
        $select_request_query->close();
        echo json_encode(["status" => "false", "message" => "Request not found or failed to retrieve user_id"]);
    }
} elseif ($action == 'rejected') {
    $update_request_query = $mysqli->prepare('UPDATE emergencyroomrequest SET status = ? WHERE request_id = ?');
    $update_request_query->bind_param('si', $action, $request_id);

    if ($update_request_query->execute()) {
        echo json_encode(["status" => "true", "message" => "Request rejected successfully"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to reject request: " . $mysqli->error]);
    }

    $update_request_query->close();
} else {
    echo json_encode(["status" => "false", "message" => "Invalid action--type 'approved' or 'rejected'"]);
}

$mysqli->close();
?>
