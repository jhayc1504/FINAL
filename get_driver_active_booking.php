<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the latest accepted booking for the driver
$sql = "SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) as commuter_name FROM bookings b JOIN users u ON b.commuter_id = u.id WHERE b.driver_id = $user_id AND b.status = 'Accepted' ORDER BY b.id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    echo json_encode(['success' => true, 'booking' => $booking]);
} else {
    echo json_encode(['success' => false, 'message' => 'No active booking found']);
}

$conn->close();
?>
