<?php
session_start();
include 'config.php';

if(isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Input validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Please fill in all fields';
        header("Location: index.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = 'Invalid email format';
        header("Location: index.php");
        exit();
    }

    // Use prepared statement
    $sql = "SELECT id, firstname, lastname, email, password, acctype FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['login_error'] = 'Database error';
        header("Location: index.php");
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['acctype'] = $user['acctype'];

            // Set role-specific user_id
            if ($user['acctype'] === 'commuter') {
                $_SESSION['commuter_user_id'] = $user['id'];
                unset($_SESSION['driver_user_id']); // Ensure no cross-contamination
                header("Location: home_commuter.html");
            } else {
                $_SESSION['driver_user_id'] = $user['id'];
                unset($_SESSION['commuter_user_id']); // Ensure no cross-contamination
                header("Location: home_driver.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = 'Incorrect email or password';
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = 'Incorrect email or password';
        header("Location: index.php");
        exit();
    }
}

?>
