<?php
session_start();
include 'config.php';

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$commuter_id = $_SESSION['user_id'];
$driver_id = NULL;
$pickup = $conn->real_escape_string($data['pickup']);
$destination = $conn->real_escape_string($data['destination']);
$landmark = $conn->real_escape_string($data['mark']);
$commuter_type = $conn->real_escape_string($data['commuterType']);
$number_regular = (int)$data['regularBoarding'];
$number_student = (int)$data['studentBoarding'];
$fare = (float)$data['estimatedFare'];
$status = 'Pending';

$sql = "INSERT INTO bookings (commuter_id, driver_id, pickup, destination, landmark, commuter_type, number_regular, number_student, status, fare) VALUES ('$commuter_id', NULL, '$pickup', '$destination', '$landmark', '$commuter_type', $number_regular, $number_student, '$status', $fare)";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'booking_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
