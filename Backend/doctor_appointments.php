<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('doctor');

$doctor_id = $_POST['doctor_id'];
$appointment_id = $_POST['appointment_id'];
$action = $_POST['action'];

$check_appointment_query = $mysqli->prepare('SELECT status FROM appointments WHERE appointment_id = ? AND doctor_id = ?');
$check_appointment_query->bind_param('ii', $appointment_id, $doctor_id);
$check_appointment_query->execute();
$appointment_status = $check_appointment_query->get_result()->fetch_assoc()['status'];
$check_appointment_query->close();

//to not update the same appointment many times
if ($appointment_status && ($appointment_status !== 'canceled' && $appointment_status !== 'completed')) {
    if ($action === 'canceled' || $action === 'completed') {
        $update_status_query = $mysqli->prepare('UPDATE appointments SET status = ? WHERE appointment_id = ?');
        $update_status_query->bind_param('si', $action, $appointment_id);

        if ($update_status_query->execute()) {
            echo json_encode(["status" => "true", "message" => "Appointment marked as $action successfully"]);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to mark appointment as $action"]);
        }

        $update_status_query->close();
    } else {
        echo json_encode(["status" => "false", "message" => "Invalid action. Use 'canceled' or 'completed'."]);
    }
} else {
    echo json_encode(["status" => "false", "message" => "Appointment not found, does not belong to the doctor, or already marked as 'canceled' or 'completed'"]);
}

$mysqli->close();
?>
