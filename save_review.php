<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = (int)$data['booking_id'];
$driver_id = (int)$data['driver_id'];
$rating = (int)$data['rating'];
$comment = $data['comment'] ?? null;
$user_id = $_SESSION['user_id'];

// Validate rating
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating']);
    exit();
}

// Check if booking belongs to user and is completed
$check_sql = "SELECT id FROM bookings WHERE id = $booking_id AND commuter_id = $user_id AND status = 'Completed'";
$result = $conn->query($check_sql);
if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking or not completed']);
    exit();
}

// Insert review
$sql = "INSERT INTO reviews (booking_id, driver_id, commuter_id, rating, comment) VALUES ($booking_id, $driver_id, $user_id, $rating, " . ($comment ? "'$comment'" : "NULL") . ")";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
