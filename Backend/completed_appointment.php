<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('doctor');

$doctor_id = $_POST['doctor_id'];
$appointment_id = $_POST['appointment_id'];

$check_appointment_query = $mysqli->prepare('SELECT * FROM appointments WHERE appointment_id = ? AND doctor_id = ?');
$check_appointment_query->bind_param('ii', $appointment_id, $doctor_id);
$check_appointment_query->execute();
$appointment = $check_appointment_query->get_result()->fetch_assoc();
$check_appointment_query->close();

if ($appointment) {
    $complete_appointment_query = $mysqli->prepare('UPDATE appointments SET status = "completed" WHERE appointment_id = ?');
    $complete_appointment_query->bind_param('i', $appointment_id);

    if ($complete_appointment_query->execute()) {
        echo json_encode(["status" => "true", "message" => "Appointment marked as completed successfully"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to mark appointment as completed"]);
    }

    $complete_appointment_query->close();
} else {
    echo json_encode(["status" => "false", "message" => "Appointment not found or does not belong to the doctor"]);
}

$mysqli->close();