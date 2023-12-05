<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('doctor');

$doctor_id = $_POST['doctor_id'];
$patient_id = $_POST['patient_id'];
$medication_name = $_POST['medication_name'];
$frequency = $_POST['frequency'];
$dosage = $_POST['dosage'];

$insert_medication_query = $mysqli->prepare('INSERT INTO medications (doctor_id, patient_id, medication_name, frequency, dosage) VALUES (?, ?, ?, ?, ?)');
$insert_medication_query->bind_param('iisss', $doctor_id, $patient_id, $medication_name, $frequency, $dosage);

if ($insert_medication_query->execute()) {
    echo json_encode(["status" => "true", "message" => "Medication prescribed successfully"]);
} else {
    echo json_encode(["status" => "false", "message" => "Failed to prescribe medication"]);
}

$insert_medication_query->close();
$mysqli->close();
?>
