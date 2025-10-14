<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@400;0,500;700;&display=swap');
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins",serif;
    }
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        color: #333;
    }
    .container{
        margin: 0 15px;
    }

    .form-box {
       width: 100%;
       max-width: 450px; 
       padding: 10px;
       background-color: whitesmoke;
       border-radius: 10px;
       box-shadow: 0 0 1 rgba(0, 0, 0, 0.1);
    }

    H2{
        font-size: 34px;
        text-align: center;
        margin-bottom: 20px;
    }

    input{
        width: 100%;
        padding: 12px;
        background: #eee;
        border-radius: 6px;
        border: none;
        outline: none;
        font-size: 16px;
        color: #333;
        margin-bottom: 20px;
    }

    button{
        width: 100%;
        padding: 12px;
        background: #7494ec;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #fff;
        font-weight: 500;
        margin-bottom: 20px;
        transition: 0.5s;
    }

    button:hover{
        background: #6884d3;
    }
    p{
        font-size: 14.5px;
        text-align: center;
        margin-bottom: 10px;
    }

    p a{
        color: #7474ec;
        text-decoration: none;
    }

    p a:hover{
        text-decoration: underline;
    }

    select{
        width: 100%;
        padding: 12px;
        background: #eee;
        border-radius: 6px;
        border: none;
        outline: none;
        font-size: 16px;
        color: #333;
        margin-bottom: 20px;
    }

    option{
        width: 100%;
        padding: 12px;
        background: #eee;
        border: none;
        outline: none;
        font-size: 16px;
        color: #333;
        margin-bottom: 20px;
    }
</style>

<?php
session_start();

$errors= [
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'register';

session_unset();

function showError($errors){
    return !empty($errors) ? "<p class='error-message'>$errors</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>

<body>
    <div class="form-box <?=isActiveForm('register', $activeForm); ?>" id="C_register-form">
            <form action="register.php" method="POST">
                <H2>REGISTER FORM</H2>
                <H3>Personal Information</H3>
                <input type="text" name="lastname" placeholder="Last Name" required>
                <input type="text" name="firstname" placeholder="First Name" required>
                <input type="text" name="middlename" placeholder="Middle Name" required>
                <H3>Birthday</H3>
                <input type="date" name="birthday" placeholder="birthday" required>
                <H3>Sex</H3>
                <select name="sex" id="sex" required>
                    <option value="select">Select an Option</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="pns">Prefer not to say</option>
                </select>
                <H3>Address</H3>
                <input type="text" name="Line1" placeholder="Address Line 1(House#, St.)" required>
                <input type="text" name="Line2" placeholder="Address(Barangay)" required>
                <input type="text" name="Line3" placeholder="Address(Municipality)" required>
                <input type="text" name="Line4" placeholder="Address(Region Province)" required>

                <H3>Account Type</H3>
                <select name="acty" id="acty" required>
                    <option value="driver" selected>Driver</option>
                </select>

                <H3>Tricycle Driver's Information</H3>
                <input type="text" name="plate" placeholder="Plate Number" required>
                <input type="text" name="franchise" placeholder="Franchise Number" required>
                <input type="text" name="license" placeholder="License Number" required>
                <H3>Email</H3>
                <?=showError($errors['register']);?>
                <input type="email" name="email" placeholder="Email" required>
                <H3>Password</H3>
                <input type="password" name="password" placeholder="Create Password" required>
                <button type="submit" name="register">Register</button>
                <p>Don't have an account? <a href="index.php">Login</a></p>
            </form>
        </div>
</body>
</html>