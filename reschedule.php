<?php

session_start();
require_once "db.php";

/* Patient must be logged in */
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION["patient_id"];


/* Check appointment ID */

if (!isset($_GET["appointment_id"])) {
    header("Location: appointments.php");
    exit;
}

$appointment_id = (int) $_GET["appointment_id"];


/* Get current appointment */

$stmt = $conn->prepare(
    "SELECT
        a.id,
        a.doctor_id,
        a.schedule_id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        d.name AS doctor_name,
        d.specialization,
        d.department
     FROM appointments a
     JOIN doctors d ON a.doctor_id = d.id
     WHERE a.id = ? AND a.patient_id = ?"
);

$stmt->bind_param("ii", $appointment_id, $patient_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    die("Appointment not found.");
}

$appointment = $result->fetch_assoc();

$stmt->close();


/* Only booked appointments can be rescheduled */

if ($appointment["status"] !== "Booked") {
    die("Only booked appointments can be rescheduled.");
}


/* Get available slots for the same doctor */

$stmt = $conn->prepare(
    "SELECT id, date, time
     FROM schedules
     WHERE doctor_id = ?
     AND status = 'Available'
     AND date >= CURDATE()
     ORDER BY date ASC, time ASC"
);

$stmt->bind_param("i", $appointment["doctor_id"]);

$stmt->execute();

$slots = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reschedule Appointment | MediCare</title>

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

        .reschedule-page {
            padding: 50px 7%;
            min-height: calc(100vh - 80px);
        }

        .page-heading {
            margin-bottom: 30px;
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

        .doctor-summary {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(50, 70, 100, 0.08);
        }

        .doctor-summary h2 {
            margin: 0 0 8px;
        }

        .doctor-summary p {
            color: #718096;
            margin: 5px 0;
        }

        .current-booking {
            margin-top: 18px;
            padding: 14px 16px;
            background: #f3f5ff;
            border-radius: 12px;
            color: #5367c8;
            font-size: 14px;
        }

        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
        }

        .slot-card {
            background: white;
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 10px 30px rgba(50, 70, 100, 0.08);
            border: 1px solid #edf0f5;
        }

        .slot-date {
            font-weight: 700;
            font-size: 17px;
            margin-bottom: 8px;
        }

        .slot-time {
            color: #718096;
            margin-bottom: 18px;
        }

        .reschedule-btn {
            width: 100%;
            border: none;
            background: #7189e8;
            color: white;
            padding: 11px;
            border-radius: 10px;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .reschedule-btn:hover {
            background: #5e75d3;
            transform: translateY(-1px);
        }

        .empty-state {
            background: white;
            border-radius: 20px;
            padding: 50px 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(50, 70, 100, 0.08);
        }

        .empty-state h2 {
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #718096;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #5e75d3;
            font-weight: 600;
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

            <a href="dashboard.php">Dashboard</a>

            <a href="doctors.php">Doctors</a>

            <a href="appointments.php">My Appointments</a>

            <a href="logout.php">Logout</a>

        </div>

    </div>

</nav>


<!-- PAGE -->

<div class="reschedule-page">

    <div class="page-heading">

        <h1>Reschedule Appointment</h1>

        <p>
            Choose a new available time for your appointment.
        </p>

    </div>


    <!-- DOCTOR INFORMATION -->

    <div class="doctor-summary">

        <h2>
            🩺 <?php echo htmlspecialchars($appointment["doctor_name"]); ?>
        </h2>

        <p>
            <?php echo htmlspecialchars($appointment["specialization"]); ?>
            •
            <?php echo htmlspecialchars($appointment["department"]); ?>
        </p>

        <div class="current-booking">

            Current appointment:
            
            <strong>
                <?php
                echo date(
                    "d M Y",
                    strtotime($appointment["appointment_date"])
                );
                ?>
                at
                <?php
                echo date(
                    "h:i A",
                    strtotime($appointment["appointment_time"])
                );
                ?>
            </strong>

        </div>

    </div>


    <!-- AVAILABLE SLOTS -->

    <?php if ($slots->num_rows > 0) { ?>

        <div class="slots-grid">

            <?php while ($slot = $slots->fetch_assoc()) { ?>

                <div class="slot-card">

                    <div class="slot-date">

                        <?php
                        echo date(
                            "d M Y",
                            strtotime($slot["date"])
                        );
                        ?>

                    </div>

                    <div class="slot-time">

                        <?php
                        echo date(
                            "h:i A",
                            strtotime($slot["time"])
                        );
                        ?>

                    </div>


                    <form
                        action="update_appointment.php"
                        method="POST"
                        onsubmit="return confirm('Reschedule this appointment to the selected slot?');"
                    >

                        <input
                            type="hidden"
                            name="appointment_id"
                            value="<?php echo $appointment_id; ?>"
                        >

                        <input
                            type="hidden"
                            name="new_schedule_id"
                            value="<?php echo $slot["id"]; ?>"
                        >

                        <button
                            type="submit"
                            class="reschedule-btn"
                        >
                            Select This Slot
                        </button>

                    </form>

                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="empty-state">

            <h2>No Other Slots Available</h2>

            <p>
                There are currently no other available slots for this doctor.
            </p>

            <a
                href="appointments.php"
                class="back-btn"
            >
                ← Back to My Appointments
            </a>

        </div>

    <?php } ?>


</div>


</body>

</html>

<?php

$stmt->close();

?>