<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
include 'header.php';
?>

    <div>
        <h1>Welcome to pasada.com</h1>
    </div>

<?php
include 'footer.php';
?>
