<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the latest accepted booking for the user
$sql = "SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) as driver_name FROM bookings b LEFT JOIN users u ON b.driver_id = u.id WHERE b.commuter_id = $user_id AND b.status = 'Accepted' ORDER BY b.id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    $booking = null;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Booking</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .booking-details { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; }
        .billing { border: 1px solid #ccc; padding: 20px; }
    </style>
</head>
<body>
    <h1>Active Booking</h1>
    <?php if ($booking): ?>
        <div class="booking-details">
            <h2>Booking Details</h2>
            <p><strong>Pickup:</strong> <?php echo htmlspecialchars($booking['pickup']); ?></p>
            <p><strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
            <p><strong>Landmark:</strong> <?php echo htmlspecialchars($booking['landmark']); ?></p>
            <p><strong>Commuter Type:</strong> <?php echo htmlspecialchars($booking['commuter_type']); ?></p>
            <p><strong>Regular Passengers:</strong> <?php echo $booking['number_regular']; ?></p>
            <p><strong>Student/SC/PWD Passengers:</strong> <?php echo $booking['number_student']; ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
        </div>
        <div class="billing">
            <h2>Billing</h2>
            <p><strong>Booking ID:</strong> <?php echo $booking['id']; ?></p>
            <p><strong>Commuter Name:</strong> <?php echo htmlspecialchars($booking['driver_name']); ?></p>
            <p><strong>Number of Commuters:</strong> <?php echo $booking['number_regular'] + $booking['number_student']; ?></p>
            <p><strong>Total Payment:</strong> ₱<?php echo number_format($booking['fare'], 2); ?></p>
        </div>
    <?php else: ?>
        <p>No active booking found.</p>
    <?php endif; ?>

    <!-- Review Modal -->
    <div id="reviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; width: 400px;">
            <h3>Rate the Driver</h3>
            <form id="reviewForm">
                <label for="rating">Rating (1-5):</label>
                <select id="rating" name="rating" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select><br><br>
                <label for="comment">Comment (optional):</label><br>
                <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>
                <button type="button" onclick="submitReview()">Submit Review</button>
                <button type="button" onclick="skipReview()">Skip</button>
            </form>
        </div>
    </div>

    <script>
        let currentBookingId = null;
        let currentDriverId = null;

        function completeBooking() {
            if (confirm('Are you sure you want to complete the booking?')) {
                // Update status to Completed in DB via AJAX
                fetch('complete_booking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ booking_id: <?php echo $booking['id']; ?> })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentBookingId = data.booking_id;
                        currentDriverId = data.driver_id;
                        document.getElementById('reviewModal').style.display = 'block';
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function submitReview() {
            const rating = document.getElementById('rating').value;
            const comment = document.getElementById('comment').value;
            if (!rating) {
                alert('Please select a rating.');
                return;
            }
            fetch('save_review.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    booking_id: currentBookingId,
                    driver_id: currentDriverId,
                    rating: parseInt(rating),
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Review submitted!');
                    window.location.href = 'home_commuter.html';
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function skipReview() {
            window.location.href = 'home_commuter.html';
        }

        // Poll for status changes
        setInterval(() => {
            fetch('get_booking_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ booking_id: <?php echo $booking['id']; ?> })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.booking.status === 'Completed') {
                    currentBookingId = data.booking.id;
                    currentDriverId = data.booking.driver_id;
                    document.getElementById('reviewModal').style.display = 'block';
                }
            });
        }, 2000);
    </script>
</body>
</html>
