<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");


authorize('doctor');


$doctor_id = $_POST['doctor_id'];
$specialization = $_POST['specialization'];
$contact_number = $_POST['contact_number'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$available_days = $_POST['available_days'];

// update the doctor's data
$query = $mysqli->prepare('UPDATE doctors SET specialization=?, contact_number=?, start_time=?, end_time=?, available_days=? WHERE doctor_id = ?');
$query->bind_param('sssssi', $specialization, $contact_number, $start_time, $end_time, $available_days, $doctor_id);

if ($query->execute()) {
    echo json_encode(["status" => "true", "message" => "Doctor updated successfully"]);
} else {
    echo json_encode(["status" => "false", "message" => "Failed to update doctor"]);
}

$query->close();
$mysqli->close();
?>
