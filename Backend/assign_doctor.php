<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('doctor');

$user_id = $_POST['user_id'];  
$doctor_id = $_POST['doctor_id']; 

$check_user_query = $mysqli->prepare('SELECT * FROM rooms WHERE user_id = ?');
$check_user_query->bind_param('i', $user_id);
$check_user_query->execute();
$room_info = $check_user_query->get_result()->fetch_assoc();
$check_user_query->close();

if ($room_info) {
    if ($room_info['doctor_id']) {
        echo json_encode(["status" => "false", "message" => "Room is already assigned to a doctor"]);
    } else {
        $assign_doctor_query = $mysqli->prepare('UPDATE rooms SET doctor_id = ? WHERE user_id = ?');
        $assign_doctor_query->bind_param('ii', $doctor_id, $user_id);

        if ($assign_doctor_query->execute()) {
            echo json_encode(["status" => "true", "message" => "Doctor assigned to the room successfully"]);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to assign doctor to the room"]);
        }

        $assign_doctor_query->close();
    }
} else {
    echo json_encode(["status" => "false", "message" => "User not found in the rooms"]);
}

$mysqli->close();
?>
