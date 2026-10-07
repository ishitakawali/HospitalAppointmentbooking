<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION["patient_id"];

if (!isset($_GET["appointment_id"])) {
    header("Location: appointments.php");
    exit;
}

$appointment_id = (int) $_GET["appointment_id"];


/* Get appointment details */

$stmt = $conn->prepare(
    "SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        d.name AS doctor_name,
        d.specialization,
        d.department,
        p.name AS patient_name
     FROM appointments a
     JOIN doctors d ON a.doctor_id = d.id
     JOIN patients p ON a.patient_id = p.id
     WHERE a.id = ? AND a.patient_id = ?"
);

$stmt->bind_param(
    "ii",
    $appointment_id,
    $patient_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    die("Appointment not found.");
}

$appointment = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Appointment Confirmed | MediCare</title>

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="css/style.css">


    <style>

        body {
            margin: 0;
            font-family: "DM Sans", sans-serif;
            background: #f5fafb;
            color: #294b5c;
        }

        .success-page {
            min-height: calc(100vh - 80px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
        }

        .success-card {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 25px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 15px 45px rgba(70, 105, 120, 0.10);
        }

        .success-icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #e4f7ec;
            color: #238653;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
        }

        .success-card h1 {
            font-family: "Playfair Display", serif;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .success-card > p {
            color: #7d8c94;
            margin-bottom: 30px;
        }

        .appointment-details {
            background: #f7fafb;
            border-radius: 16px;
            padding: 22px;
            text-align: left;
            margin-bottom: 25px;
        }

        .appointment-details h3 {
            margin-top: 0;
            color: #294b5c;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #e6eef0;
            font-size: 14px;
        }

        .detail:last-child {
            border-bottom: none;
        }

        .detail span:first-child {
            color: #89969d;
        }

        .detail span:last-child {
            font-weight: 600;
            text-align: right;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .primary-btn {
            background: #4c91a3;
            color: white;
        }

        .secondary-btn {
            background: #eef6f7;
            color: #4c91a3;
        }

        @media (max-width: 600px) {

            .success-card {
                padding: 30px 20px;
            }

            .success-card h1 {
                font-size: 27px;
            }

        }

    </style>

</head>


<body>


<header class="navbar">

    <div class="logo">

        <span class="logo-icon">✚</span>

        <span>MediCare</span>

    </div>


    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="doctors.php">
            Doctors
        </a>

        <a href="appointments.php">
            My Appointments
        </a>

    </nav>


    <div class="nav-buttons">

        <a href="logout.php" class="register-btn">
            Logout
        </a>

    </div>

</header>


<div class="success-page">

    <div class="success-card">


        <div class="success-icon">
            ✓
        </div>


        <h1>
            Appointment Confirmed!
        </h1>


        <p>
            Your appointment has been successfully booked.
        </p>


        <div class="appointment-details">

            <h3>
                Appointment Details
            </h3>


            <div class="detail">

                <span>
                    Patient
                </span>

                <span>
                    <?= htmlspecialchars($appointment["patient_name"]) ?>
                </span>

            </div>


            <div class="detail">

                <span>
                    Doctor
                </span>

                <span>
                    <?= htmlspecialchars($appointment["doctor_name"]) ?>
                </span>

            </div>


            <div class="detail">

                <span>
                    Specialization
                </span>

                <span>
                    <?= htmlspecialchars($appointment["specialization"]) ?>
                </span>

            </div>


            <div class="detail">

                <span>
                    Department
                </span>

                <span>
                    <?= htmlspecialchars($appointment["department"]) ?>
                </span>

            </div>


            <div class="detail">

                <span>
                    Date
                </span>

                <span>
                    <?= date(
                        "d M Y",
                        strtotime($appointment["appointment_date"])
                    ) ?>
                </span>

            </div>


            <div class="detail">

                <span>
                    Time
                </span>

                <span>
                    <?= date(
                        "h:i A",
                        strtotime($appointment["appointment_time"])
                    ) ?>
                </span>

            </div>


        </div>


        <div class="actions">

            <a
                href="dashboard.php"
                class="btn secondary-btn"
            >
                Dashboard
            </a>

            <a
                href="appointments.php"
                class="btn primary-btn"
            >
                My Appointments
            </a>

        </div>


    </div>

</div>


</body>

</html>