<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

// Input validation
if (!$data || !isset($data['booking_id']) || !isset($data['driver_id']) || !isset($data['rating'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$booking_id = (int)$data['booking_id'];
$driver_id = (int)$data['driver_id'];
$rating = (int)$data['rating'];
$comment = isset($data['comment']) ? trim($data['comment']) : null;
$user_id = $_SESSION['user_id'];

// Validate rating
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating']);
    exit();
}

// Validate comment length if provided
if ($comment !== null && strlen($comment) > 500) {
    echo json_encode(['success' => false, 'message' => 'Comment too long']);
    exit();
}

// Check if booking belongs to user and is completed
$check_sql = "SELECT id FROM bookings WHERE id = ? AND commuter_id = ? AND status = 'Completed'";
$stmt = $conn->prepare($check_sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking or not completed']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Check if review already exists
$review_check_sql = "SELECT id FROM reviews WHERE booking_id = ? AND commuter_id = ?";
$review_stmt = $conn->prepare($review_check_sql);
if (!$review_stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}
$review_stmt->bind_param("ii", $booking_id, $user_id);
$review_stmt->execute();
$review_result = $review_stmt->get_result();

if ($review_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Review already submitted']);
    $review_stmt->close();
    $conn->close();
    exit();
}
$review_stmt->close();

// Insert review
$insert_sql = "INSERT INTO reviews (booking_id, driver_id, commuter_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}

$comment_param = $comment ?: null;
$stmt->bind_param("iiiis", $booking_id, $driver_id, $user_id, $rating, $comment_param);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
