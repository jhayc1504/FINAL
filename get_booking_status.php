<?php
session_start();
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = (int)$data['booking_id'];

$sql = "SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) as commuter_name FROM bookings b JOIN users u ON b.commuter_id = u.id WHERE b.id = $booking_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    echo json_encode(['success' => true, 'booking' => $booking]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
}

$conn->close();
?>
