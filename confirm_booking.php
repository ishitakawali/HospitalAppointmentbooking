<?php

session_start();
require_once "db.php";

/* Patient must be logged in */
if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION["patient_id"];


/* Check required data */

if (
    !isset($_POST["schedule_id"]) ||
    !isset($_POST["doctor_id"])
) {
    header("Location: doctors.php");
    exit;
}

$schedule_id = (int) $_POST["schedule_id"];
$doctor_id = (int) $_POST["doctor_id"];


/* Get selected schedule */

$stmt = $conn->prepare(
    "SELECT id, doctor_id, date, time, status
     FROM schedules
     WHERE id = ? AND doctor_id = ?"
);

$stmt->bind_param(
    "ii",
    $schedule_id,
    $doctor_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    die("Invalid appointment slot.");

}

$schedule = $result->fetch_assoc();

$stmt->close();


/* Check if slot is still available */

if ($schedule["status"] !== "Available") {

    die("Sorry, this appointment slot is no longer available.");

}


/* Start transaction */

$conn->begin_transaction();

try {

    /* Insert appointment */

    $stmt = $conn->prepare(
        "INSERT INTO appointments
        (
            patient_id,
            doctor_id,
            schedule_id,
            appointment_date,
            appointment_time,
            status
        )
        VALUES (?, ?, ?, ?, ?, 'Booked')"
    );

    $stmt->bind_param(
        "iiiss",
        $patient_id,
        $doctor_id,
        $schedule_id,
        $schedule["date"],
        $schedule["time"]
    );

    if (!$stmt->execute()) {
        throw new Exception("Could not create appointment.");
    }


    /* IMPORTANT:
       Store the appointment ID immediately
       after INSERT.
    */

    $appointment_id = $conn->insert_id;

    $stmt->close();


    /* Mark schedule as booked */

    $stmt = $conn->prepare(
        "UPDATE schedules
         SET status = 'Booked'
         WHERE id = ? AND status = 'Available'"
    );

    $stmt->bind_param(
        "i",
        $schedule_id
    );

    $stmt->execute();

    if ($stmt->affected_rows !== 1) {

        $stmt->close();

        throw new Exception(
            "This appointment slot has already been booked."
        );

    }

    $stmt->close();


    /* Everything successful */

    $conn->commit();


    /* Redirect using the saved appointment ID */

    header(
        "Location: appointment_success.php?appointment_id="
        . $appointment_id
    );

    exit;


} catch (Exception $e) {

    $conn->rollback();

    die(
        "Unable to book the appointment. "
        . htmlspecialchars($e->getMessage())
    );

}

?>