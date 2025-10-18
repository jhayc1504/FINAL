<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['commuter_user_id'] ?? $_SESSION['user_id'];

// Fetch the latest accepted booking for the user
$sql = "SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) as driver_name FROM bookings b LEFT JOIN users u ON b.driver_id = u.id WHERE b.commuter_id = ? AND b.status = 'Accepted' ORDER BY b.id DESC LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    echo json_encode(['success' => true, 'booking' => $booking]);
} else {
    echo json_encode(['success' => false, 'message' => 'No active booking']);
}

$stmt->close();
$conn->close();
?>
