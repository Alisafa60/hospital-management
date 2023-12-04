<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");

$patient_id = $_POST['patient_id'];
$user_id = $_POST['user_id'];
$last_name = $_POST['last_name'];

$query = $mysqli->prepare('SELECT * FROM patients WHERE patient_id = ? OR user_id=? OR last_name=?');
$query->bind_param('iis', $patient_id, $user_id, $last_name);
$query->execute();
$result = $query->get_result();

$patientData = $result->fetch_assoc();

echo json_encode(["status" => "true", "patient" => $patientData]);

$query->close();
$mysqli->close();
?>
