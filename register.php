<?php
include 'config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Input validation and sanitization
    $lastname = isset($_POST["lastname"]) ? trim($_POST["lastname"]) : "";
    $firstname = isset($_POST["firstname"]) ? trim($_POST["firstname"]) : "";
    $middlename = isset($_POST["middlename"]) ? trim($_POST["middlename"]) : "";
    $birthday = isset($_POST["birthday"]) ? trim($_POST["birthday"]) : "";
    $sex = isset($_POST["sex"]) ? trim($_POST["sex"]) : "";
    $Line1 = isset($_POST["Line1"]) ? trim($_POST["Line1"]) : "";
    $Line2 = isset($_POST["Line2"]) ? trim($_POST["Line2"]) : "";
    $Line3 = isset($_POST["Line3"]) ? trim($_POST["Line3"]) : "";
    $Line4 = isset($_POST["Line4"]) ? trim($_POST["Line4"]) : "";
    $acty = isset($_POST["acty"]) ? trim($_POST["acty"]) : "";
    $plate = isset($_POST["plate"]) ? trim($_POST["plate"]) : "";
    $franchise = isset($_POST["franchise"]) ? trim($_POST["franchise"]) : "";
    $license = isset($_POST["license"]) ? trim($_POST["license"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    // Validation
    if (empty($firstname) || empty($lastname) || empty($birthday) || empty($sex) || empty($Line1) || empty($Line2) || empty($Line3) || empty($Line4) || empty($acty) || empty($email) || empty($password)) {
        $_SESSION['register_error'] = 'All required fields must be filled!';
        redirectBasedOnAccountType($acty);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = 'Invalid email format!';
        redirectBasedOnAccountType($acty);
        exit();
    }

    if (!in_array($sex, ['Male', 'Female', 'Prefer not to say'])) {
        $_SESSION['register_error'] = 'Invalid sex selection!';
        redirectBasedOnAccountType($acty);
        exit();
    }

    if (!in_array($acty, ['commuter', 'driver'])) {
        $_SESSION['register_error'] = 'Invalid account type!';
        redirectBasedOnAccountType($acty);
        exit();
    }

    if ($acty === 'driver' && (empty($plate) || empty($franchise) || empty($license))) {
        $_SESSION['register_error'] = 'Driver information is required!';
        redirectBasedOnAccountType($acty);
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['register_error'] = 'Password must be at least 6 characters long!';
        redirectBasedOnAccountType($acty);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    if (!$stmt) {
        $_SESSION['register_error'] = 'Database error!';
        redirectBasedOnAccountType($acty);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $_SESSION['register_error'] = 'Email is already registered!';
        $stmt->close();
        redirectBasedOnAccountType($acty);
        exit();
    }
    $stmt->close();

    // Insert new user
    $insert_sql = "INSERT INTO users (lastname, firstname, middlename, birthday, sex, aline1, aline2, aline3, aline4, acctype, platenum, franchisenum, licensenum, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        $_SESSION['register_error'] = 'Database error!';
        redirectBasedOnAccountType($acty);
        exit();
    }
    $stmt->bind_param("sssssssssssssss", $lastname, $firstname, $middlename, $birthday, $sex, $Line1, $Line2, $Line3, $Line4, $acty, $plate, $franchise, $license, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['register_error'] = 'Registration failed: ' . $conn->error;
        redirectBasedOnAccountType($acty);
        exit();
    }

    $stmt->close();
}

function redirectBasedOnAccountType($acty) {
    if(strtolower($acty) == 'commuter'){
        header("Location: commuter_register.php");
    }else{
        header("Location: driver_register.php");
    }
}
?>
