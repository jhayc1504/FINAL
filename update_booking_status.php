<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

// Input validation
if (!$data || !isset($data['booking_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$booking_id = (int)$data['booking_id'];
$status = trim($data['status']);
$fare = isset($data['fare']) ? (float)$data['fare'] : 0;
$user_id = $_SESSION['driver_user_id'] ?? $_SESSION['user_id'];

// Validate status
$valid_statuses = ['Pending', 'Accepted', 'Completed', 'Declined'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Validate fare
if ($fare < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid fare']);
    exit();
}

// Check if booking exists and is in correct state for update
$check_sql = "SELECT status FROM bookings WHERE id = ?";
$stmt = $conn->prepare($check_sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    $stmt->close();
    $conn->close();
    exit();
}

$current_status = $result->fetch_assoc()['status'];
$stmt->close();



// Business logic validation
if ($status === 'Accepted') {
    // Ensure only drivers can accept bookings
    if ($_SESSION['acctype'] !== 'driver') {
        echo json_encode(['success' => false, 'message' => 'Only drivers can accept bookings']);
        $conn->close();
        exit();
    }

    // Prevent accepting own booking
    $check_commuter_sql = "SELECT commuter_id FROM bookings WHERE id = ?";
    $stmt_check = $conn->prepare($check_commuter_sql);
    if (!$stmt_check) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    $stmt_check->bind_param("i", $booking_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        $commuter_id = $result_check->fetch_assoc()['commuter_id'];
        if ($commuter_id == $user_id) {
            echo json_encode(['success' => false, 'message' => 'Cannot accept your own booking']);
            $stmt_check->close();
            $conn->close();
            exit();
        }
    }
    $stmt_check->close();

    if ($current_status !== 'Pending') {
        echo json_encode(['success' => false, 'message' => 'Can only accept pending bookings']);
        $conn->close();
        exit();
    }
}

if ($status === 'Completed' && $current_status !== 'Accepted') {
    echo json_encode(['success' => false, 'message' => 'Can only complete accepted bookings']);
    $conn->close();
    exit();
}

// Update booking
if ($status === 'Accepted') {
    $update_sql = "UPDATE bookings SET status = ?, fare = ?, driver_id = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("sdii", $status, $fare, $user_id, $booking_id);
} else {
    $update_sql = "UPDATE bookings SET status = ?, fare = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("sdi", $status, $fare, $booking_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
