<?php

require_once "db.php";

$message = "";
$message_type = "";

// Handle registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Check passwords
    if ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        // Check if email already exists
        $check = $conn->prepare(
            "SELECT id FROM patients WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "An account with this email already exists.";
            $message_type = "error";

        } else {

            // Securely hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert patient
            $stmt = $conn->prepare(
                "INSERT INTO patients (name, email, password, phone)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashed_password,
                $phone
            );

            if ($stmt->execute()) {

                // Registration successful
                header("Location: login.php?registered=1");
                exit;

            } else {

                $message = "Registration failed. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Registration | MediCare</title>

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

        .register-page {
            min-height: calc(100vh - 82px);
            padding: 60px 7%;
            background: #f5fafb;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            width: 100%;
            max-width: 1050px;
            display: grid;
            grid-template-columns: 0.85fr 1.15fr;
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(70, 105, 120, 0.10);
        }

        /* LEFT SIDE */

        .register-info {
            padding: 55px 45px;
            background: #e6f3f5;
            position: relative;
        }

        .register-info .small-title {
            color: #4c91a3;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .register-info h1 {
            font-family: 'Playfair Display', serif;
            color: #294b5c;
            font-size: 40px;
            line-height: 1.2;
            margin: 18px 0;
        }

        .register-info h1 em {
            color: #4c91a3;
            font-style: normal;
        }

        .register-info > p {
            color: #71808a;
            font-size: 14px;
            line-height: 1.8;
        }

        .benefits {
            margin-top: 35px;
        }

        .benefit {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .benefit-icon {
            width: 42px;
            height: 42px;
            background: white;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
        }

        .benefit strong {
            display: block;
            color: #3b5967;
            font-size: 13px;
        }

        .benefit small {
            color: #829099;
            font-size: 11px;
        }

        /* FORM SIDE */

        .register-form {
            padding: 50px 55px;
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

        /* MESSAGE */

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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 18px;
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
            padding: 13px 14px;
            border: 1px solid #dfe8eb;
            border-radius: 10px;
            outline: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            color: #435c67;
            background: #fbfcfd;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #6ba8b6;
            background: white;
            box-shadow: 0 0 0 3px rgba(76, 145, 163, 0.08);
        }

        .terms {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 5px 0 20px;
            color: #89969d;
            font-size: 11px;
            line-height: 1.5;
        }

        .terms input {
            margin-top: 2px;
            accent-color: #4c91a3;
        }

        .create-account {
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

        .create-account:hover {
            background: #39788a;
            transform: translateY(-2px);
        }

        .login-text {
            text-align: center;
            margin-top: 20px;
            color: #89969d;
            font-size: 12px;
        }

        .login-text a {
            color: #4c91a3;
            font-weight: 600;
        }

        /* RESPONSIVE */

        @media (max-width: 800px) {

            .register-container {
                grid-template-columns: 1fr;
            }

            .register-info {
                padding: 40px;
            }

            .register-form {
                padding: 40px;
            }
        }

        @media (max-width: 550px) {

            .register-page {
                padding: 30px 5%;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .register-info,
            .register-form {
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


    <!-- REGISTRATION -->

    <main class="register-page">

        <div class="register-container">

            <!-- LEFT SIDE -->

            <div class="register-info">

                <span class="small-title">
                    WELCOME TO MEDICARE
                </span>

                <h1>
                    Start your journey
                    towards <em>better care.</em>
                </h1>

                <p>
                    Create your patient account to easily book,
                    manage and keep track of your hospital appointments.
                </p>

                <div class="benefits">

                    <div class="benefit">

                        <div class="benefit-icon">📅</div>

                        <div>
                            <strong>Easy appointment booking</strong>
                            <small>Book appointments in just a few clicks.</small>
                        </div>

                    </div>

                    <div class="benefit">

                        <div class="benefit-icon">👨‍⚕️</div>

                        <div>
                            <strong>Find trusted doctors</strong>
                            <small>Browse doctors by specialization.</small>
                        </div>

                    </div>

                    <div class="benefit">

                        <div class="benefit-icon">🔔</div>

                        <div>
                            <strong>Manage your appointments</strong>
                            <small>View, reschedule or cancel anytime.</small>
                        </div>

                    </div>

                </div>

            </div>


            <!-- FORM SIDE -->

            <div class="register-form">

                <div class="form-heading">

                    <h2>Create your account</h2>

                    <p>
                        Enter your details to get started.
                    </p>

                </div>


                <!-- ERROR MESSAGE -->

                <?php if ($message): ?>

                    <div class="form-message <?= $message_type ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>

                <?php endif; ?>


                <!-- REGISTRATION FORM -->

                <form action="" method="POST">

                    <div class="form-row">

                        <div class="form-group">

                            <label>Full Name</label>

                            <input
                                type="text"
                                name="name"
                                placeholder="Enter your full name"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>Phone Number</label>

                            <input
                                type="tel"
                                name="phone"
                                placeholder="Enter phone number"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label>Email Address</label>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            required
                        >

                    </div>


                    <div class="form-row">

                        <div class="form-group">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                placeholder="Create a password"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>Confirm Password</label>

                            <input
                                type="password"
                                name="confirm_password"
                                placeholder="Confirm password"
                                required
                            >

                        </div>

                    </div>


                    <div class="terms">

                        <input
                            type="checkbox"
                            required
                        >

                        <span>
                            I agree to the terms and conditions
                            and confirm that the information provided is correct.
                        </span>

                    </div>


                    <button
                        type="submit"
                        class="create-account"
                    >
                        Create Patient Account →
                    </button>

                </form>


                <div class="login-text">

                    Already have an account?

                    <a href="login.php">
                        Login here
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