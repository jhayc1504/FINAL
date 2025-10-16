<?php
session_start();
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

// Input validation
if (!$data || !isset($data['booking_id']) || !is_numeric($data['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

$booking_id = (int)$data['booking_id'];

$sql = "SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) as commuter_name FROM bookings b JOIN users u ON b.commuter_id = u.id WHERE b.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    echo json_encode(['success' => true, 'booking' => $booking]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
}

$stmt->close();
$conn->close();
?>
