<?php

session_start();
require_once "db.php";

/* Fetch all doctors from database */
$sql = "SELECT id, name, specialization, department FROM doctors ORDER BY id ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching doctors: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find a Doctor | MediCare</title>

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

        /* ================================
           DOCTORS PAGE
        ================================= */

        .doctors-page {
            min-height: calc(100vh - 82px);
            background: #f5fafb;
            padding: 60px 7%;
        }


        /* HEADING */

        .doctors-heading {
            text-align: center;
            margin-bottom: 45px;
        }

        .doctors-heading h1 {
            font-family: 'Playfair Display', serif;
            color: #294b5c;
            font-size: 40px;
            margin: 0 0 10px;
        }

        .doctors-heading p {
            color: #829198;
            font-size: 14px;
            margin: 0;
        }

        .doctor-count {
            display: inline-block;
            margin-top: 18px;
            padding: 9px 18px;
            border-radius: 30px;
            background: #e6f3f5;
            color: #4c91a3;
            font-size: 12px;
            font-weight: 600;
        }


        /* ================================
           DOCTOR GRID
        ================================= */

        .doctors-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 28px !important;
            width: 100%;
            box-sizing: border-box;
        }


        /* ================================
           DOCTOR CARD
        ================================= */

        .doctor-card {
            position: relative !important;
            display: block !important;
            width: 100% !important;
            min-width: 0;
            box-sizing: border-box;

            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;

            border: 1px solid #edf3f4;

            box-shadow:
                0 10px 35px rgba(70, 105, 120, 0.07);

            transition: transform 0.3s ease,
                        box-shadow 0.3s ease;
        }

        .doctor-card:hover {
            transform: translateY(-6px);

            box-shadow:
                0 16px 42px rgba(70, 105, 120, 0.12);
        }


        /* ================================
           DOCTOR IMAGE
        ================================= */

        .doctor-image {
            height: 210px;

            background:
                linear-gradient(
                    135deg,
                    #dceff2,
                    #eee7f7
                );

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 70px;
        }


        /* ================================
           DOCTOR INFORMATION
        ================================= */

        .doctor-info {
            padding: 24px;
        }

        .doctor-info h3 {
            color: #294b5c;
            font-size: 18px;
            margin: 0 0 8px;
        }

        .specialization {
            color: #4c91a3;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .department {
            color: #929da2;
            font-size: 12px;
            margin-bottom: 22px;
        }


        /* ================================
           BUTTON
        ================================= */

        .schedule-btn {
            display: block;

            width: 100%;
            box-sizing: border-box;

            text-align: center;
            text-decoration: none;

            background: #4c91a3;
            color: white;

            padding: 12px;

            border-radius: 9px;

            font-size: 12px;
            font-weight: 600;

            transition: background 0.3s ease;
        }

        .schedule-btn:hover {
            background: #39788a;
        }


        /* ================================
           TABLET
        ================================= */

        @media (max-width: 950px) {

            .doctors-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

        }


        /* ================================
           MOBILE
        ================================= */

        @media (max-width: 600px) {

            .doctors-page {
                padding: 40px 5%;
            }

            .doctors-heading h1 {
                font-size: 30px;
            }

            .doctors-grid {
                grid-template-columns: 1fr !important;
                gap: 20px !important;
            }

        }

    </style>

</head>


<body>


<!-- ================================
     NAVBAR
================================= -->

<header class="navbar">

    <div class="logo">

        <span class="logo-icon">
            ✚
        </span>

        <span>
            MediCare
        </span>

    </div>


    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="doctors.php">
            Doctors
        </a>

        <a href="index.php#services">
            Services
        </a>

        <a href="index.php#how-it-works">
            How It Works
        </a>

    </nav>


    <div class="nav-buttons">

        <?php if (isset($_SESSION["patient_id"])): ?>

            <a
                href="dashboard.php"
                class="login-btn"
            >
                Dashboard
            </a>

            <a
                href="logout.php"
                class="register-btn"
            >
                Logout
            </a>

        <?php else: ?>

            <a
                href="login.php"
                class="login-btn"
            >
                Login
            </a>

            <a
                href="register.php"
                class="register-btn"
            >
                Register
            </a>

        <?php endif; ?>

    </div>

</header>



<!-- ================================
     MAIN DOCTORS SECTION
================================= -->

<main class="doctors-page">


    <div class="doctors-heading">

        <h1>
            Find the Right Doctor
        </h1>

        <p>
            Choose a specialist and book an appointment at your convenience.
        </p>

        <div class="doctor-count">

            <?= $result->num_rows ?>

            Doctors Available

        </div>

    </div>



    <!-- ================================
         DOCTOR CARDS
    ================================= -->

    <div class="doctors-grid">


        <?php while ($doctor = $result->fetch_assoc()): ?>


            <div class="doctor-card">


                <!-- Doctor Image -->

                <div class="doctor-image">
                    🩺
                </div>


                <!-- Doctor Information -->

                <div class="doctor-info">


                    <h3>

                        <?= htmlspecialchars($doctor["name"]) ?>

                    </h3>


                    <div class="specialization">

                        <?= htmlspecialchars($doctor["specialization"]) ?>

                    </div>


                    <div class="department">

                        <?= htmlspecialchars($doctor["department"]) ?>

                    </div>


                    <a
                        href="book.php?doctor_id=<?= (int)$doctor["id"] ?>"
                        class="schedule-btn"
                    >

                        View Schedule →

                    </a>


                </div>

            </div>


        <?php endwhile; ?>


    </div>

</main>



<!-- ================================
     FOOTER
================================= -->

<footer>

    <div class="footer-logo">

        <span>
            ✚
        </span>

        MediCare

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