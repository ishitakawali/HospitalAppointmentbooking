<?php

session_start();
require_once "db.php";

/* Patient must be logged in */
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION["patient_id"];

/* Check doctor ID */
if (!isset($_GET["doctor_id"]) || !is_numeric($_GET["doctor_id"])) {
    header("Location: doctors.php");
    exit;
}

$doctor_id = (int) $_GET["doctor_id"];


/* Fetch doctor details */

$stmt = $conn->prepare(
    "SELECT id, name, specialization, department
     FROM doctors
     WHERE id = ?"
);

$stmt->bind_param("i", $doctor_id);
$stmt->execute();

$doctor_result = $stmt->get_result();

if ($doctor_result->num_rows !== 1) {
    header("Location: doctors.php");
    exit;
}

$doctor = $doctor_result->fetch_assoc();

$stmt->close();


/* Fetch available schedules */

$stmt = $conn->prepare(
    "SELECT id, date, time
     FROM schedules
     WHERE doctor_id = ?
     AND status = 'Available'
     AND date >= CURDATE()
     ORDER BY date ASC, time ASC"
);

$stmt->bind_param("i", $doctor_id);
$stmt->execute();

$schedule_result = $stmt->get_result();

$stmt->close();

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Book Appointment | MediCare
    </title>


    <!-- Google Fonts -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="css/style.css"
    >


    <style>

        /* =========================
           BOOKING PAGE
        ========================= */

        .booking-page {

            min-height: calc(100vh - 82px);

            background: #f5fafb;

            padding: 55px 7%;

        }


        .booking-container {

            max-width: 1000px;

            margin: auto;

        }


        /* HEADER */

        .booking-heading {

            text-align: center;

            margin-bottom: 35px;

        }


        .booking-heading h1 {

            font-family: 'Playfair Display', serif;

            color: #294b5c;

            font-size: 38px;

            margin-bottom: 10px;

        }


        .booking-heading p {

            color: #829198;

            font-size: 14px;

        }


        /* DOCTOR CARD */

        .doctor-summary {

            background: white;

            border-radius: 20px;

            padding: 25px;

            display: flex;

            align-items: center;

            gap: 20px;

            margin-bottom: 30px;

            border: 1px solid #edf3f4;

            box-shadow:
                0 10px 35px rgba(70, 105, 120, 0.07);

        }


        .doctor-avatar {

            width: 70px;

            height: 70px;

            border-radius: 18px;

            background:
                linear-gradient(
                    135deg,
                    #dceff2,
                    #eee7f7
                );

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 35px;

            flex-shrink: 0;

        }


        .doctor-summary h2 {

            color: #294b5c;

            font-size: 20px;

            margin-bottom: 5px;

        }


        .doctor-summary .specialization {

            color: #4c91a3;

            font-size: 13px;

            font-weight: 600;

            margin-bottom: 4px;

        }


        .doctor-summary .department {

            color: #929da2;

            font-size: 12px;

        }


        /* SCHEDULE SECTION */

        .schedule-box {

            background: white;

            border-radius: 20px;

            padding: 30px;

            border: 1px solid #edf3f4;

            box-shadow:
                0 10px 35px rgba(70, 105, 120, 0.07);

        }


        .schedule-box h2 {

            font-family: 'Playfair Display', serif;

            color: #294b5c;

            font-size: 24px;

            margin-bottom: 8px;

        }


        .schedule-description {

            color: #8b989e;

            font-size: 13px;

            margin-bottom: 25px;

        }


        /* SCHEDULE GRID */

        .schedule-grid {

            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 18px;

        }


        .schedule-option {

            border: 1px solid #dfecee;

            border-radius: 15px;

            padding: 20px;

            background: #fbfdfd;

            transition: 0.25s;

        }


        .schedule-option:hover {

            border-color: #4c91a3;

            transform: translateY(-3px);

            box-shadow:
                0 8px 25px rgba(70, 105, 120, 0.08);

        }


        .schedule-date {

            color: #294b5c;

            font-weight: 700;

            font-size: 14px;

            margin-bottom: 7px;

        }


        .schedule-time {

            color: #4c91a3;

            font-size: 13px;

            margin-bottom: 17px;

        }


        .book-btn {

            width: 100%;

            border: none;

            cursor: pointer;

            padding: 10px;

            border-radius: 9px;

            background: #4c91a3;

            color: white;

            font-family: 'DM Sans', sans-serif;

            font-size: 12px;

            font-weight: 600;

            transition: 0.3s;

        }


        .book-btn:hover {

            background: #39788a;

        }


        /* EMPTY */

        .no-slots {

            text-align: center;

            padding: 45px 20px;

            background: #f8fbfb;

            border-radius: 15px;

            border: 1px dashed #d8e5e8;

        }


        .no-slots-icon {

            font-size: 35px;

            margin-bottom: 12px;

        }


        .no-slots h3 {

            color: #526a74;

            font-size: 16px;

            margin-bottom: 7px;

        }


        .no-slots p {

            color: #929da2;

            font-size: 12px;

        }


        /* RESPONSIVE */

        @media (max-width: 850px) {

            .schedule-grid {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }

        }


        @media (max-width: 600px) {

            .booking-page {

                padding: 40px 5%;

            }

            .booking-heading h1 {

                font-size: 30px;

            }

            .doctor-summary {

                align-items: flex-start;

            }

            .schedule-grid {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

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

    </div>

</header>



<!-- =========================
     BOOKING PAGE
========================= -->

<main class="booking-page">

    <div class="booking-container">


        <div class="booking-heading">

            <h1>
                Book Your Appointment
            </h1>

            <p>
                Select a convenient date and time for your visit.
            </p>

        </div>



        <!-- DOCTOR INFORMATION -->

        <div class="doctor-summary">

            <div class="doctor-avatar">
                🩺
            </div>


            <div>

                <h2>
                    <?= htmlspecialchars($doctor["name"]) ?>
                </h2>

                <div class="specialization">

                    <?= htmlspecialchars($doctor["specialization"]) ?>

                </div>

                <div class="department">

                    <?= htmlspecialchars($doctor["department"]) ?>

                </div>

            </div>

        </div>



        <!-- AVAILABLE SCHEDULES -->

        <div class="schedule-box">

            <h2>
                Available Time Slots
            </h2>

            <p class="schedule-description">

                Choose one of the available appointment slots below.

            </p>



            <?php if ($schedule_result->num_rows > 0): ?>


                <div class="schedule-grid">


                    <?php while ($schedule = $schedule_result->fetch_assoc()): ?>


                        <div class="schedule-option">


                            <div class="schedule-date">

                                <?= date(
                                    "d M Y",
                                    strtotime($schedule["date"])
                                ) ?>

                            </div>


                            <div class="schedule-time">

                                🕐

                                <?= date(
                                    "h:i A",
                                    strtotime($schedule["time"])
                                ) ?>

                            </div>


                            <form
                                action="confirm_booking.php"
                                method="POST"
                            >

                                <input
                                    type="hidden"
                                    name="schedule_id"
                                    value="<?= (int)$schedule["id"] ?>"
                                >


                                <input
                                    type="hidden"
                                    name="doctor_id"
                                    value="<?= (int)$doctor["id"] ?>"
                                >


                                <button
                                    type="submit"
                                    class="book-btn"
                                >
                                    Book This Slot
                                </button>

                            </form>


                        </div>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <div class="no-slots">

                    <div class="no-slots-icon">
                        📅
                    </div>

                    <h3>
                        No available slots
                    </h3>

                    <p>
                        This doctor currently has no available appointments.
                    </p>

                </div>


            <?php endif; ?>


        </div>


    </div>

</main>



<!-- =========================
     FOOTER
========================= -->

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