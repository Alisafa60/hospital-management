<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('patient');


$patient_id = $_POST['patient_id'];
$doctor_id = $_POST['doctor_id'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];

$query = $mysqli->prepare('SELECT start_time, end_time, available_days FROM doctors WHERE doctor_id = ?');
$query->bind_param('i', $doctor_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $start_time = $doctor['start_time'];
    $end_time = $doctor['end_time'];
    $available_days = explode(',', $doctor['available_days']);

    // Check if the selected day is among the available days
    $selected_day = date('l', strtotime($appointment_date)); 
    if (in_array($selected_day, $available_days)) {

        // Check if the selected time is within the doctor's working hours
        if ($appointment_time >= $start_time && $appointment_time <= $end_time) {

            // Check if the selected time slot is available
            $appointment_query = $mysqli->prepare('SELECT * FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?');
            $appointment_query->bind_param('iss', $doctor_id, $appointment_date, $appointment_time);
            $appointment_query->execute();
            $existing_appointment = $appointment_query->get_result()->fetch_assoc();

            if (!$existing_appointment) {
                $insert_query = $mysqli->prepare('INSERT INTO appointments (doctor_id, patient_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, "scheduled")');
                $insert_query->bind_param('iiss', $doctor_id, $patient_id, $appointment_date, $appointment_time);

                if ($insert_query->execute()) {
                    echo json_encode(["status" => "true", "message" => "Appointment scheduled successfully"]);
                } else {
                    echo json_encode(["status" => "false", "message" => "Failed to schedule appointment"]);
                }

                $insert_query->close();
            } else {
                echo json_encode(["status" => "false", "message" => "Selected time slot is not available"]);
            }

            $appointment_query->close();
        } else {
            echo json_encode(["status" => "false", "message" => "Selected time is outside working hours"]);
        }
    } else {
        echo json_encode(["status" => "false", "message" => "Doctor is not available on the selected day"]);
    }
} else {
    echo json_encode(["status" => "false", "message" => "Doctor not found"]);
}

$query->close();
$mysqli->close();
?>