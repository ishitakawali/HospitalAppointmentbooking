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


/* Get appointment */

$stmt = $conn->prepare(
    "SELECT id, schedule_id, status
     FROM appointments
     WHERE id = ? AND patient_id = ?"
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


/* Check appointment status */

if ($appointment["status"] === "Cancelled") {
    header("Location: appointments.php");
    exit;
}


/* Start transaction */

$conn->begin_transaction();

try {

    /* Cancel appointment */

    $stmt = $conn->prepare(
        "UPDATE appointments
         SET status = 'Cancelled'
         WHERE id = ? AND patient_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $appointment_id,
        $patient_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Could not cancel appointment.");
    }

    $stmt->close();


    /* Make the schedule available again */

    $stmt = $conn->prepare(
        "UPDATE schedules
         SET status = 'Available'
         WHERE id = ?"
    );

    $stmt->bind_param(
        "i",
        $appointment["schedule_id"]
    );

    if (!$stmt->execute()) {
        throw new Exception("Could not release appointment slot.");
    }

    $stmt->close();


    /* Save changes */

    $conn->commit();

    header("Location: appointments.php");
    exit;


} catch (Exception $e) {

    $conn->rollback();

    die(
        "Unable to cancel appointment. "
        . htmlspecialchars($e->getMessage())
    );
}

?>