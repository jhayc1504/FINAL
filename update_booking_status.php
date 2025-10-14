<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = (int)$data['booking_id'];
$status = $conn->real_escape_string($data['status']);
$fare = isset($data['fare']) ? (float)$data['fare'] : 0;

$user_id = $_SESSION['user_id'];

if ($status === 'Accepted') {
    $sql = "UPDATE bookings SET status = '$status', fare = $fare, driver_id = $user_id WHERE id = $booking_id";
} else {
    $sql = "UPDATE bookings SET status = '$status', fare = $fare WHERE id = $booking_id";
}

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
