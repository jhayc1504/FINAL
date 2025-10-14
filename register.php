<?php
include 'config.php';

session_start();

$lastname = "";
$firstname = "";
$middlename = "";
$birthday = "";
$sex = "";
$Line1 = "";
$Line2 = "";
$Line3 = "";
$Line4 = "";
$acty = "";
$plate = "";
$franchise = "";
$license = "";
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lastname = isset($_POST["lastname"]) ? $conn->real_escape_string(test_function($_POST["lastname"])) : "";
    $firstname = isset($_POST["firstname"]) ? $conn->real_escape_string(test_function($_POST["firstname"])) : "";
    $middlename = isset($_POST["middlename"]) ? $conn->real_escape_string(test_function($_POST["middlename"])) : "";
    $birthday = isset($_POST["birthday"]) ? $conn->real_escape_string(test_function($_POST["birthday"])) : "";
    $sex = isset($_POST["sex"]) ? $conn->real_escape_string(test_function($_POST["sex"])) : "";
    $Line1 = isset($_POST["Line1"]) ? $conn->real_escape_string(test_function($_POST["Line1"])) : "";
    $Line2 = isset($_POST["Line2"]) ? $conn->real_escape_string(test_function($_POST["Line2"])) : "";
    $Line3 = isset($_POST["Line3"]) ? $conn->real_escape_string(test_function($_POST["Line3"])) : "";
    $Line4 = isset($_POST["Line4"]) ? $conn->real_escape_string(test_function($_POST["Line4"])) : "";
    $acty = isset($_POST["acty"]) ? $conn->real_escape_string(test_function($_POST["acty"])) : "";
    $plate = isset($_POST["plate"]) ? $conn->real_escape_string(test_function($_POST["plate"])) : "";
    $franchise = isset($_POST["franchise"]) ? $conn->real_escape_string(test_function($_POST["franchise"])) : "";
    $license = isset($_POST["license"]) ? $conn->real_escape_string(test_function($_POST["license"])) : "";
    $email = isset($_POST["email"]) ? $conn->real_escape_string(test_function($_POST["email"])) : "";
    $password = isset($_POST["password"]) ? password_hash(test_function($_POST["password"]), PASSWORD_DEFAULT) : "";


    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");

if($checkEmail->num_rows > 0){
    $_SESSION['register_error'] = 'Email is already registered!';
    if(strtolower($acty) == 'commuter'){
        header("Location: commuter_register.php");
    }else{
        header("Location: driver_register.php");
    }
    exit();
}else{
    $conn->query("INSERT INTO users (lastname, firstname, middlename, birthday, sex, aline1, aline2, aline3, aline4, acctype, platenum, franchisenum, licensenum, email, password) VALUES ('$lastname', '$firstname', '$middlename', '$birthday', '$sex', '$Line1', '$Line2', '$Line3', '$Line4', '$acty', '$plate', '$franchise', '$license', '$email', '$password')");
    header("Location: index.php");
    exit();
}
    

}

function test_function($data): string {
    $data = trim($data);
    return $data;
}
?>
