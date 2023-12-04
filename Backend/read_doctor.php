<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");


$doctor_id = $_POST['doctor_id'];
$user_id = $_POST['user_id'];
$last_name = $_POST['last_name'];

// Fetch the doctor's data
$query = $mysqli->prepare('SELECT * FROM doctors WHERE doctor_id = ? OR user_id = ? or last_name=?');
$query->bind_param('iis', $doctor_id, $user_id, $last_name);
$query->execute();
$result = $query->get_result();

$doctorData = $result->fetch_assoc();

echo json_encode(["status" => "true", "doctor" => $doctorData]);

$query->close();
$mysqli->close();
?>
