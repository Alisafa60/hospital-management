<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('patient');

$patient_id = $_POST['patient_id'];
$date_of_birth = $_POST['date_of_birth'];
$gender = $_POST['gender'];
$blood_type = $_POST['blood_type'];
$contact_number = $_POST['contact_number'];
$medical_history = $_POST['medical_history'];
$insurance_details = $_POST['insurance_details'];

// Update the patient's data
$query = $mysqli->prepare('UPDATE patients SET date_of_birth=?, gender=?, blood_type=?, contact_number=?,
medical_history=?, insurance_details=? WHERE patient_id = ?');
$query->bind_param('ssssssi', $date_of_birth, $gender, $blood_type, $contact_number, $medical_history, $insurance_details, $patient_id);

if ($query->execute()) {
    echo json_encode(["status" => "true", "message" => "Patient updated successfully"]);
} else {
    echo json_encode(["status" => "false", "message" => "Failed to update patient"]);
}

$query->close();
$mysqli->close();
?>
