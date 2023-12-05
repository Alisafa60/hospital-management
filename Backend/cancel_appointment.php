<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('patient');
authorize('doctor');

$patient_id = $_POST['patient_id'];
$appointment_id = $_POST['appointment_id'];

$check_appointment_query = $mysqli->prepare('SELECT * FROM appointments WHERE appointment_id = ? AND patient_id = ?');
$check_appointment_query->bind_param('ii', $appointment_id, $patient_id);
$check_appointment_query->execute();
$appointment = $check_appointment_query->get_result()->fetch_assoc();
$check_appointment_query->close();

if ($appointment) {
    $cancel_appointment_query = $mysqli->prepare('UPDATE appointments SET status = "canceled" WHERE appointment_id = ?');
    $cancel_appointment_query->bind_param('i', $appointment_id);

    if ($cancel_appointment_query->execute()) {
        echo json_encode(["status" => "true", "message" => "Appointment cancelled successfully"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to cancel appointment"]);
    }

    $cancel_appointment_query->close();
} else {
    echo json_encode(["status" => "false", "message" => "Appointment not found or does not belong to the patient"]);
}

$mysqli->close();
?>
