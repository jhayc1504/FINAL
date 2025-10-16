<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

// Input validation
if (!$data || !isset($data['booking_id']) || !is_numeric($data['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

$booking_id = (int)$data['booking_id'];
$user_id = $_SESSION['user_id'];

// First check if booking exists and belongs to user and is in Accepted status
$check_sql = "SELECT id, status FROM bookings WHERE id = ? AND driver_id = ?";
$stmt = $conn->prepare($check_sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found or access denied']);
    $stmt->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
if ($row['status'] !== 'Accepted') {
    echo json_encode(['success' => false, 'message' => 'Booking is not in accepted status']);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

// Update booking status to Completed
$update_sql = "UPDATE bookings SET status = 'Completed' WHERE id = ?";
$stmt = $conn->prepare($update_sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    // Fetch the booking details including driver_id
    $fetch_sql = "SELECT driver_id FROM bookings WHERE id = ?";
    $fetch_stmt = $conn->prepare($fetch_sql);
    if (!$fetch_stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $fetch_stmt->bind_param("i", $booking_id);
    $fetch_stmt->execute();
    $fetch_result = $fetch_stmt->get_result();

    if ($fetch_result->num_rows > 0) {
        $fetch_row = $fetch_result->fetch_assoc();
        echo json_encode(['success' => true, 'booking_id' => $booking_id, 'driver_id' => $fetch_row['driver_id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found after update']);
    }
    $fetch_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
