<?php

session_start();
require_once "db.php";

/* Patient must be logged in */
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION["patient_id"];


/* Get patient's appointments */

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
     ORDER BY a.appointment_date ASC, a.appointment_time ASC"
);

$stmt->bind_param("i", $patient_id);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Appointments | MediCare</title>

    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        body {
            margin: 0;
            font-family: "DM Sans", sans-serif;
            background: #f7f9fc;
            color: #263238;
        }

        .appointments-page {
            padding: 50px 7%;
            min-height: calc(100vh - 80px);
        }

        .page-heading {
            margin-bottom: 35px;
        }

        .page-heading h1 {
            font-family: "Playfair Display", serif;
            font-size: 36px;
            margin: 0 0 8px;
        }

        .page-heading p {
            color: #718096;
            margin: 0;
        }

        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(300px, 1fr)
            );
            gap: 25px;
        }

        .appointment-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(50, 70, 100, 0.09);
            border: 1px solid #edf0f5;
        }

        .doctor-top {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .doctor-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            background: #edf1ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
        }

        .doctor-top h3 {
            margin: 0 0 4px;
            font-size: 18px;
        }

        .doctor-top p {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }

        .appointment-details {
            background: #f8f9fc;
            border-radius: 14px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            font-size: 14px;
        }

        .detail span:first-child {
            color: #7b8794;
        }

        .detail span:last-child {
            font-weight: 600;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background: #e7f8ef;
            color: #238653;
            font-size: 12px;
            font-weight: 700;
        }

        .appointment-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .reschedule-btn {
            display: inline-block;
            text-decoration: none;
            background: #edf1ff;
            color: #5e75d3;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .reschedule-btn:hover {
            background: #e0e6ff;
        }

        .cancel-btn {
            display: inline-block;
            text-decoration: none;
            background: #fff0f0;
            color: #d9534f;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .cancel-btn:hover {
            background: #ffe0e0;
        }

        .cancelled-text {
            color: #d9534f;
            font-size: 14px;
            font-weight: 600;
        }

        .empty-state {
            background: white;
            border-radius: 22px;
            padding: 55px 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(50, 70, 100, 0.08);
        }

        .empty-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .empty-state h2 {
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #718096;
            margin-bottom: 25px;
        }

        .book-btn {
            display: inline-block;
            text-decoration: none;
            background: #7189e8;
            color: white;
            padding: 12px 22px;
            border-radius: 11px;
            font-weight: 600;
        }

        .book-btn:hover {
            background: #5e75d3;
        }

        @media (max-width: 600px) {

            .appointments-page {
                padding: 35px 20px;
            }

            .page-heading h1 {
                font-size: 30px;
            }

        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

    <div class="nav-container">

        <a href="dashboard.php" class="logo">
            Medi<span>Care</span>
        </a>

        <div class="nav-links">

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="doctors.php">
                Doctors
            </a>

            <a href="appointments.php">
                My Appointments
            </a>

            <a href="logout.php">
                Logout
            </a>

        </div>

    </div>

</nav>


<!-- PAGE -->

<div class="appointments-page">

    <div class="page-heading">

        <h1>My Appointments</h1>

        <p>
            View and manage your scheduled appointments.
        </p>

    </div>


    <?php if ($result->num_rows > 0) { ?>

        <div class="appointments-grid">

            <?php while ($appointment = $result->fetch_assoc()) { ?>

                <div class="appointment-card">


                    <!-- DOCTOR -->

                    <div class="doctor-top">

                        <div class="doctor-icon">
                            🩺
                        </div>

                        <div>

                            <h3>

                                <?php
                                echo htmlspecialchars(
                                    $appointment["doctor_name"]
                                );
                                ?>

                            </h3>

                            <p>

                                <?php
                                echo htmlspecialchars(
                                    $appointment["specialization"]
                                );
                                ?>

                            </p>

                        </div>

                    </div>


                    <!-- DETAILS -->

                    <div class="appointment-details">


                        <div class="detail">

                            <span>
                                Department
                            </span>

                            <span>

                                <?php
                                echo htmlspecialchars(
                                    $appointment["department"]
                                );
                                ?>

                            </span>

                        </div>


                        <div class="detail">

                            <span>
                                Date
                            </span>

                            <span>

                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $appointment["appointment_date"]
                                    )
                                );
                                ?>

                            </span>

                        </div>


                        <div class="detail">

                            <span>
                                Time
                            </span>

                            <span>

                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $appointment["appointment_time"]
                                    )
                                );
                                ?>

                            </span>

                        </div>


                        <div class="detail">

                            <span>
                                Status
                            </span>

                            <span>

                                <span class="status">

                                    <?php
                                    echo htmlspecialchars(
                                        $appointment["status"]
                                    );
                                    ?>

                                </span>

                            </span>

                        </div>


                    </div>


                    <!-- ACTION BUTTONS -->

                    <?php if ($appointment["status"] === "Booked") { ?>

                        <div class="appointment-actions">


                            <a
                                href="reschedule.php?appointment_id=<?php echo $appointment["id"]; ?>"
                                class="reschedule-btn"
                            >
                                Reschedule
                            </a>


                            <a
                                href="cancel_appointment.php?appointment_id=<?php echo $appointment["id"]; ?>"
                                class="cancel-btn"
                                onclick="return confirm('Are you sure you want to cancel this appointment?');"
                            >
                                Cancel Appointment
                            </a>


                        </div>

                    <?php } else { ?>

                        <div class="cancelled-text">
                            This appointment has been cancelled.
                        </div>

                    <?php } ?>


                </div>

            <?php } ?>

        </div>


    <?php } else { ?>


        <!-- NO APPOINTMENTS -->

        <div class="empty-state">

            <div class="empty-icon">
                📅
            </div>

            <h2>
                No Appointments Yet
            </h2>

            <p>
                You don't have any appointments scheduled.
            </p>

            <a
                href="doctors.php"
                class="book-btn"
            >
                Book an Appointment
            </a>

        </div>


    <?php } ?>


</div>


</body>

</html>

<?php

$stmt->close();

?>