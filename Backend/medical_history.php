<?php
header('Access-Control-Allow-Origin: *');
include("db_connection.php");
include("meddleware.php");

authorize('doctor');

$user_id = $_POST['user_id']; 
$doctor_id = $_POST['doctor_id']; 
$note_text = $_POST['note_text'];

$check_user_query = $mysqli->prepare('SELECT * FROM users WHERE user_id = ? AND role = "patient"');
$check_user_query->bind_param('i', $user_id);
$check_user_query->execute();
$user_exists = $check_user_query->fetch();
$check_user_query->close();

if ($user_exists) {
    $insert_note_query = $mysqli->prepare('INSERT INTO medical_history (user_id, doctor_id, note_text) VALUES (?, ?, ?)');
    $insert_note_query->bind_param('iis', $user_id, $doctor_id, $note_text);

    if ($insert_note_query->execute()) {
        echo json_encode(["status" => "true", "message" => "Medical history note added successfully"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Failed to add medical history note"]);
    }

    $insert_note_query->close();
} else {
    echo json_encode(["status" => "false", "message" => "User not found or does not have the role of 'patient'"]);
}

$mysqli->close();
?>
