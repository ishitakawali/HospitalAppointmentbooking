<?php

session_start();
require_once "db.php";

/* If patient is not logged in */
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION["patient_id"];
$patient_name = $_SESSION["patient_name"];


/* Get upcoming booked appointment */

$stmt = $conn->prepare(
    "SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        d.name AS doctor_name,
        d.specialization,
        d.department
     FROM appointments a
     JOIN doctors d ON a.doctor_id = d.id
     WHERE a.patient_id = ?
     AND a.status = 'Booked'
     AND a.appointment_date >= CURDATE()
     ORDER BY a.appointment_date ASC, a.appointment_time ASC
     LIMIT 1"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$appointment_result = $stmt->get_result();

$upcoming_appointment = null;

if ($appointment_result->num_rows > 0) {
    $upcoming_appointment = $appointment_result->fetch_assoc();
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Dashboard | MediCare</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="css/style.css">


    <style>

        .dashboard-page {
            min-height: calc(100vh - 82px);
            background: #f5fafb;
            padding: 55px 7%;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .dashboard-header h1 {
            font-family: 'Playfair Display', serif;
            color: #294b5c;
            font-size: 34px;
            margin-bottom: 7px;
        }

        .dashboard-header p {
            color: #7d8c94;
            font-size: 14px;
        }

        .welcome-name {
            color: #4c91a3;
        }

        .dashboard-badge {
            background: #e6f3f5;
            color: #4c91a3;
            padding: 10px 17px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }


        /* QUICK ACTIONS */

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
            margin-bottom: 35px;
        }

        .dashboard-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 10px 35px rgba(70, 105, 120, 0.07);
            border: 1px solid #edf3f4;
            transition: 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(70, 105, 120, 0.11);
        }

        .card-icon {
            width: 52px;
            height: 52px;
            border-radius: 15px;
            background: #e6f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            margin-bottom: 18px;
        }

        .dashboard-card h3 {
            color: #294b5c;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .dashboard-card p {
            color: #89969d;
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .dashboard-btn {
            display: inline-block;
            padding: 10px 17px;
            border-radius: 9px;
            background: #4c91a3;
            color: white;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .dashboard-btn:hover {
            background: #39788a;
        }

        .dashboard-btn.secondary {
            background: #eef6f7;
            color: #4c91a3;
        }

        .dashboard-btn.secondary:hover {
            background: #e1eff1;
        }


        /* APPOINTMENT SECTION */

        .appointment-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 35px rgba(70, 105, 120, 0.07);
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title h2 {
            font-family: 'Playfair Display', serif;
            color: #294b5c;
            font-size: 23px;
            margin: 0;
        }

        .section-title a {
            color: #4c91a3;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }


        /* UPCOMING APPOINTMENT */

        .appointment-card {
            background: #f7fafb;
            border: 1px solid #e2edef;
            border-radius: 16px;
            padding: 22px;
        }

        .appointment-doctor {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .appointment-doctor-icon {
            width: 48px;
            height: 48px;
            border-radius: 13px;
            background: #e6f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .appointment-doctor h3 {
            color: #294b5c;
            font-size: 16px;
            margin: 0 0 4px;
        }

        .appointment-doctor p {
            color: #7d8c94;
            font-size: 12px;
            margin: 0;
        }

        .appointment-info {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .info-box {
            background: white;
            border-radius: 10px;
            padding: 11px 15px;
            font-size: 12px;
            color: #63757e;
        }

        .info-box strong {
            color: #294b5c;
            margin-left: 4px;
        }

        .appointment-status {
            display: inline-block;
            background: #e7f7ed;
            color: #238653;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }


        /* EMPTY */

        .empty-appointment {
            background: #f7fafb;
            border: 1px dashed #d8e5e8;
            border-radius: 14px;
            padding: 30px;
            text-align: center;
        }

        .empty-appointment .empty-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .empty-appointment h3 {
            color: #49616c;
            font-size: 15px;
            margin-bottom: 6px;
        }

        .empty-appointment p {
            color: #94a1a7;
            font-size: 12px;
        }


        @media (max-width: 700px) {

            .dashboard-page {
                padding: 35px 5%;
            }

            .dashboard-header {
                align-items: flex-start;
                gap: 15px;
                flex-direction: column;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
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

        <a href="dashboard.php" class="login-btn">
            Dashboard
        </a>

        <a href="logout.php" class="register-btn">
            Logout
        </a>

    </div>

</header>



<!-- DASHBOARD -->

<main class="dashboard-page">


    <div class="dashboard-header">

        <div>

            <h1>

                Welcome,

                <span class="welcome-name">

                    <?= htmlspecialchars($patient_name) ?>

                </span>

                👋

            </h1>

            <p>
                Manage your healthcare appointments from one place.
            </p>

        </div>


        <div class="dashboard-badge">

            Patient Account

        </div>

    </div>



    <!-- QUICK ACTIONS -->

    <div class="dashboard-grid">


        <!-- BOOK -->

        <div class="dashboard-card">

            <div class="card-icon">
                📅
            </div>

            <h3>
                Book an Appointment
            </h3>

            <p>
                Find a doctor and choose an available date and time
                for your hospital visit.
            </p>

            <a
                href="doctors.php"
                class="dashboard-btn"
            >
                Book Now →
            </a>

        </div>



        <!-- DOCTORS -->

        <div class="dashboard-card">

            <div class="card-icon">
                🩺
            </div>

            <h3>
                Find a Doctor
            </h3>

            <p>
                Browse our doctors and explore their specializations
                and available schedules.
            </p>

            <a
                href="doctors.php"
                class="dashboard-btn secondary"
            >
                View Doctors →
            </a>

        </div>


    </div>



    <!-- APPOINTMENTS -->

    <section class="appointment-section">


        <div class="section-title">

            <h2>
                My Appointments
            </h2>

            <a href="appointments.php">
                View All →
            </a>

        </div>



        <?php if ($upcoming_appointment) { ?>


            <div class="appointment-card">


                <div class="appointment-doctor">


                    <div class="appointment-doctor-icon">
                        🩺
                    </div>


                    <div>

                        <h3>

                            <?= htmlspecialchars(
                                $upcoming_appointment["doctor_name"]
                            ) ?>

                        </h3>

                        <p>

                            <?= htmlspecialchars(
                                $upcoming_appointment["specialization"]
                            ) ?>

                            •
                            
                            <?= htmlspecialchars(
                                $upcoming_appointment["department"]
                            ) ?>

                        </p>

                    </div>


                </div>



                <div class="appointment-info">


                    <div class="info-box">

                        📅

                        <strong>

                            <?= date(
                                "d M Y",
                                strtotime(
                                    $upcoming_appointment["appointment_date"]
                                )
                            ) ?>

                        </strong>

                    </div>


                    <div class="info-box">

                        🕐

                        <strong>

                            <?= date(
                                "h:i A",
                                strtotime(
                                    $upcoming_appointment["appointment_time"]
                                )
                            ) ?>

                        </strong>

                    </div>


                    <div class="info-box">

                        <span class="appointment-status">
                            Booked
                        </span>

                    </div>


                </div>


                <a
                    href="appointments.php"
                    class="dashboard-btn secondary"
                >
                    Manage Appointment →
                </a>


            </div>


        <?php } else { ?>


            <div class="empty-appointment">


                <div class="empty-icon">
                    📋
                </div>


                <h3>
                    No upcoming appointments
                </h3>


                <p>
                    Book an appointment with one of our doctors to see it here.
                </p>


                <br>


                <a
                    href="doctors.php"
                    class="dashboard-btn"
                >
                    Book Appointment →
                </a>


            </div>


        <?php } ?>


    </section>


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