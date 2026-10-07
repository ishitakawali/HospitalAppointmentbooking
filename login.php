<?php

session_start();
require_once "db.php";

$message = "";
$message_type = "";

// Registration success message
if (isset($_GET["registered"])) {
    $message = "Registration successful! Please log in.";
    $message_type = "success";
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Find patient by email
    $stmt = $conn->prepare(
        "SELECT id, name, email, password FROM patients WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $patient = $result->fetch_assoc();

        // Check password
        if (password_verify($password, $patient["password"])) {

            // Create login session
            $_SESSION["patient_id"] = $patient["id"];
            $_SESSION["patient_name"] = $patient["name"];
            $_SESSION["patient_email"] = $patient["email"];

            // Go to dashboard
            header("Location: dashboard.php");
            exit;

        } else {

            $message = "Invalid email or password.";
            $message_type = "error";
        }

    } else {

        $message = "Invalid email or password.";
        $message_type = "error";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Login | MediCare</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>

        .login-page {
            min-height: calc(100vh - 82px);
            padding: 60px 7%;
            background: #f5fafb;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(70, 105, 120, 0.10);
        }

        /* LEFT SIDE */

        .login-info {
            padding: 60px 50px;
            background: #e6f3f5;
        }

        .login-info .small-title {
            color: #4c91a3;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .login-info h1 {
            font-family: 'Playfair Display', serif;
            color: #294b5c;
            font-size: 40px;
            line-height: 1.2;
            margin: 18px 0;
        }

        .login-info h1 em {
            color: #4c91a3;
            font-style: normal;
        }

        .login-info p {
            color: #71808a;
            font-size: 14px;
            line-height: 1.8;
        }

        .login-benefit {
            margin-top: 35px;
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .login-icon {
            width: 44px;
            height: 44px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .login-benefit strong {
            display: block;
            color: #3b5967;
            font-size: 13px;
        }

        .login-benefit small {
            color: #829099;
            font-size: 11px;
        }

        /* FORM */

        .login-form {
            padding: 60px 55px;
        }

        .form-heading h2 {
            font-family: 'Playfair Display', serif;
            color: #294b5c;
            font-size: 30px;
        }

        .form-heading p {
            color: #89969d;
            font-size: 13px;
            margin-top: 7px;
            margin-bottom: 28px;
        }

        .form-message {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .form-message.error {
            background: #fff0f0;
            color: #c44b4b;
            border: 1px solid #ffd5d5;
        }

        .form-message.success {
            background: #eefaf5;
            color: #3d8b6b;
            border: 1px solid #ccefe0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #4c626d;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #dfe8eb;
            border-radius: 10px;
            outline: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            color: #435c67;
            background: #fbfcfd;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #6ba8b6;
            background: white;
            box-shadow: 0 0 0 3px rgba(76, 145, 163, 0.08);
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: -8px;
            margin-bottom: 24px;
            color: #4c91a3;
            font-size: 11px;
            text-decoration: none;
        }

        .login-submit {
            width: 100%;
            border: none;
            padding: 14px;
            border-radius: 10px;
            background: #4c91a3;
            color: white;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-submit:hover {
            background: #39788a;
            transform: translateY(-2px);
        }

        .register-text {
            text-align: center;
            margin-top: 22px;
            color: #89969d;
            font-size: 12px;
        }

        .register-text a {
            color: #4c91a3;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 800px) {

            .login-container {
                grid-template-columns: 1fr;
            }

            .login-info {
                padding: 40px;
            }

            .login-form {
                padding: 40px;
            }
        }

        @media (max-width: 550px) {

            .login-page {
                padding: 30px 5%;
            }

            .login-info,
            .login-form {
                padding: 30px 25px;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <header class="navbar">

        <div class="logo">
            <span class="logo-icon">✚</span>
            <span>MediCare</span>
        </div>

        <nav>
            <a href="index.php">Home</a>
            <a href="doctors.php">Doctors</a>
            <a href="index.php#services">Services</a>
            <a href="index.php#how-it-works">How It Works</a>
        </nav>

        <div class="nav-buttons">
            <a href="login.php" class="login-btn">Login</a>
            <a href="register.php" class="register-btn">Register</a>
        </div>

    </header>


    <!-- LOGIN -->

    <main class="login-page">

        <div class="login-container">

            <!-- LEFT -->

            <div class="login-info">

                <span class="small-title">
                    WELCOME BACK
                </span>

                <h1>
                    Your healthcare,
                    <em>all in one place.</em>
                </h1>

                <p>
                    Login to manage your appointments,
                    find doctors and stay connected with your healthcare journey.
                </p>

                <div class="login-benefit">

                    <div class="login-icon">📅</div>

                    <div>
                        <strong>Manage appointments</strong>
                        <small>View and manage your upcoming visits.</small>
                    </div>

                </div>

                <div class="login-benefit">

                    <div class="login-icon">🩺</div>

                    <div>
                        <strong>Find your doctor</strong>
                        <small>Browse doctors and available schedules.</small>
                    </div>

                </div>

            </div>


            <!-- FORM -->

            <div class="login-form">

                <div class="form-heading">

                    <h2>Login to your account</h2>

                    <p>
                        Enter your registered email and password.
                    </p>

                </div>


                <!-- MESSAGE -->

                <?php if ($message): ?>

                    <div class="form-message <?= $message_type ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>

                <?php endif; ?>


                <form action="" method="POST">

                    <div class="form-group">

                        <label>Email Address</label>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                    </div>


                    <a href="#" class="forgot-password">
                        Forgot password?
                    </a>


                    <button
                        type="submit"
                        class="login-submit"
                    >
                        Login to MediCare →
                    </button>

                </form>


                <div class="register-text">

                    Don't have an account?

                    <a href="register.php">
                        Create an account
                    </a>

                </div>

            </div>

        </div>

    </main>


    <!-- FOOTER -->

    <footer>

        <div class="footer-logo">
            <span>✚</span> MediCare
        </div>

        <p>
            Hospital Appointment Booking System
        </p>

        <p class="copyright">
            © 2026 MediCare. All rights reserved.
        </p>

    </footer>

</body>

</html>