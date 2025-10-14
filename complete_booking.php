<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = (int)$data['booking_id'];
$user_id = $_SESSION['user_id'];

$sql = "UPDATE bookings SET status = 'Completed' WHERE id = $booking_id";

if ($conn->query($sql) === TRUE) {
    // Fetch the booking details including driver_id
    $fetch_sql = "SELECT driver_id FROM bookings WHERE id = $booking_id";
    $result = $conn->query($fetch_sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'booking_id' => $booking_id, 'driver_id' => $row['driver_id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
