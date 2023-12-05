<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");


authorize('admin');

// if the request is to add a new room
if ($_POST['action'] == 'add_room') {
    $room_number = $_POST['room_number'];

    // Check if the room already exists
    $check_room_query = $mysqli->prepare('SELECT room_number FROM rooms WHERE room_number = ?');
    $check_room_query->bind_param('s', $room_number);
    $check_room_query->execute();
    $check_room_query->store_result();

    if ($check_room_query->num_rows > 0) {
        // room already exists
        echo json_encode(["status" => "false", "message" => "Room with the specified number already exists"]);
    } else {
        // doesn't exist, add a new room
        $add_room_query = $mysqli->prepare('INSERT INTO rooms (room_number, availability) VALUES (?, "free")');
        $add_room_query->bind_param('s', $room_number);

        if ($add_room_query->execute()) {
            echo json_encode(["status" => "true", "message" => "Room added successfully", "room_number" => $room_number]);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to add room: " . $mysqli->error]);
        }

        $add_room_query->close();
    }

    $check_room_query->close();
} elseif ($_POST['action'] == 'update_room_status') {
    $room_number = $_POST['room_number'];
    $new_status = $_POST['new_status'];

    // if the room exists
    $check_room_query = $mysqli->prepare('SELECT room_number FROM rooms WHERE room_number = ?');
    $check_room_query->bind_param('s', $room_number);
    $check_room_query->execute();
    $check_room_query->store_result();

    if ($check_room_query->num_rows > 0) {
        // update its status
        $update_room_query = $mysqli->prepare('UPDATE rooms SET availability = ? WHERE room_number = ?');
        $update_room_query->bind_param('ss', $new_status, $room_number);

        if ($update_room_query->execute()) {
            echo json_encode(["status" => "true", "message" => "Room status updated successfully", "room_number" => $room_number, "new_status" => $new_status]);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to update room status: " . $mysqli->error]);
        }

        $update_room_query->close();
    } else {
        // room doesn't exist
        echo json_encode(["status" => "false", "message" => "Room with the specified number does not exist"]);
    }

    $check_room_query->close();
} else {
    echo json_encode(["status" => "false", "message" => "Invalid action"]);
}

$mysqli->close();
?>
