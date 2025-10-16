<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the oldest pending booking for drivers to accept
$sql = "SELECT b.id as booking_id, b.pickup, b.destination, b.landmark, b.commuter_type, b.number_regular, b.number_student, b.fare, b.status FROM bookings b WHERE b.status = 'Pending' AND b.driver_id IS NULL ORDER BY b.id ASC LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}

// No parameters needed for this query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    // Calculate estimatedFare if needed (for pending bookings)
    if ($booking['status'] === 'Pending') {
        $estimatedFare = 0;
        if ($booking['commuter_type'] === 'Regular') {
            $estimatedFare = $booking['number_regular'] * 20;
        } elseif ($booking['commuter_type'] === 'Student/SC/PWD') {
            $estimatedFare = $booking['number_student'] * 15;
        } elseif ($booking['commuter_type'] === 'Both') {
            $estimatedFare = $booking['number_regular'] * 20 + $booking['number_student'] * 15;
        }
        $booking['estimatedFare'] = $estimatedFare;
    }
    echo json_encode(['success' => true, 'booking' => $booking]);
} else {
    echo json_encode(['success' => false, 'message' => 'No active booking']);
}

$stmt->close();
$conn->close();
?>
