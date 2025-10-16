<?php
session_start();
include 'config.php';


if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Input validation
if (!$data || !isset($data['pickup']) || !isset($data['destination']) || !isset($data['commuterType'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$commuter_id = $_SESSION['user_id'];
$driver_id = NULL;
$pickup = trim($data['pickup']);
$destination = trim($data['destination']);
$landmark = trim($data['mark'] ?? '');
$commuter_type = $data['commuterType'];
$number_regular = (int)($data['regularBoarding'] ?? 0);
$number_student = (int)($data['studentBoarding'] ?? 0);
$fare = (float)$data['estimatedFare'];
$status = 'Pending';

// Validate inputs
if (empty($pickup) || empty($destination) || !in_array($commuter_type, ['Regular', 'Student/SC/PWD', 'Both'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

if ($number_regular < 0 || $number_student < 0 || $fare < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid numeric values']);
    exit;
}

// Use prepared statement
$sql = "INSERT INTO bookings (commuter_id, driver_id, pickup, destination, landmark, commuter_type, number_regular, number_student, status, fare) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("issssissd", $commuter_id, $pickup, $destination, $landmark, $commuter_type, $number_regular, $number_student, $status, $fare);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'booking_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
