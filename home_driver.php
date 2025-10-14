<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<style>
    *{
        padding: 0px;
        margin: 0px;
        box-sizing: border-box;
    }

    /* Desktop styles */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #333;
        padding: 10px 20px;
    }

    /* Adjust hamburger and navMenu layout in navbar */
    .navbar .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        margin-left: auto;
    }

    .branding {
        color: white;
        font-size: 24px;
        text-decoration: none;
    }

    .navMenu {
        list-style: none;
        display: flex;
    }

    .navMenu li {
        margin-left: 20px;
    }

    .navlink {
        color: white;
        text-decoration: none;
        font-size: 18px;
    }

    .navlink:hover {
        text-decoration: underline;
    }

    .bar {
        height: 3px;
        width: 25px;
        background-color: white;
        margin: 4px 0;
        border-radius: 2px;
        transition: 0.3s;
    }

    /* Mobile styles */
    @media (max-width: 768px) {
        .navMenu {
            position: fixed;
            top: 60px;
            right: -100%;
            height: 100vh;
            width: 200px;
            background-color: #333;
            flex-direction: column;
            align-items: start;
            padding-top: 20px;
            transition: right 0.3s ease-in-out;
            z-index: 10;
        }

        .navMenu.active {
            right: 0;
        }

        .navMenu li {
            margin: 15px 20px;
        }

        .navbar .hamburger {
            display: flex;
            margin-left: 20px;
        }

        /* Animate hamburger bars when active */
        .hamburger.active .bar:nth-child(1) {
            transform: rotate(45deg) translate(8px, 10px);
        }

        .hamburger.active .bar:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active .bar:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -10px);
        }
    }
</style>

<body>
    <header>
        <nav class="navbar">
            <a href="" class="branding">PASADA.com</a>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
            <ul class="navMenu">
                <li>
                    <a href="#" class="navlink">Home</a>
                </li>

                <li>
                    <a href="#" class="navlink">Booking</a>
                </li>

                <li>
                    <a href="logout.php" class="navlink">Logout</a>
                </li>
            </ul>
        </nav>
    </header>

    <H1>Wellcome to Pasada.com
        this is the driver hpmepage 
    </H1>

    <script src="script.js"></script>
</body>
</html>
