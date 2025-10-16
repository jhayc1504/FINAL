<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all pending bookings for drivers to accept
$sql = "SELECT b.id as booking_id, b.pickup, b.destination, b.landmark, b.commuter_type, b.number_regular, b.number_student FROM bookings b WHERE b.status = 'Pending' AND b.driver_id IS NULL ORDER BY b.id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    // Calculate estimatedFare
    $estimatedFare = 0;
    if ($row['commuter_type'] === 'Regular') {
        $estimatedFare = $row['number_regular'] * 20;
    } elseif ($row['commuter_type'] === 'Student/SC/PWD') {
        $estimatedFare = $row['number_student'] * 15;
    } elseif ($row['commuter_type'] === 'Both') {
        $estimatedFare = $row['number_regular'] * 20 + $row['number_student'] * 15;
    }
    $row['estimatedFare'] = $estimatedFare;
    $bookings[] = $row;
}

echo json_encode(['success' => true, 'bookings' => $bookings]);

$stmt->close();
$conn->close();
?>
