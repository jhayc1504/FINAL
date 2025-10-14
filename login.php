<?php
session_start();
include 'config.php';

if(isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['acctype'] = $user['acctype'];

            if ($user['acctype'] === 'commuter'){
                header("Location: home_commuter.html");
            } else {
                header("Location: home_driver.html");
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    header("Location: index.php");
    exit();
}

?>
