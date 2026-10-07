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
    !isset($_POST["appointment_id"]) ||
    !isset($_POST["new_schedule_id"])
) {
    header("Location: appointments.php");
    exit;
}

$appointment_id = (int) $_POST["appointment_id"];
$new_schedule_id = (int) $_POST["new_schedule_id"];


/* Get current appointment */

$stmt = $conn->prepare(
    "SELECT id, doctor_id, schedule_id, status
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


/* Only booked appointments can be rescheduled */

if ($appointment["status"] !== "Booked") {
    die("This appointment cannot be rescheduled.");
}


/* Get new schedule */

$stmt = $conn->prepare(
    "SELECT id, doctor_id, date, time, status
     FROM schedules
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $new_schedule_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    die("Selected appointment slot not found.");
}

$new_schedule = $result->fetch_assoc();

$stmt->close();


/* Make sure new slot belongs to same doctor */

if ((int)$new_schedule["doctor_id"] !== (int)$appointment["doctor_id"]) {
    die("Invalid appointment slot.");
}


/* Make sure new slot is available */

if ($new_schedule["status"] !== "Available") {
    die("Sorry, this appointment slot is no longer available.");
}


/* Start transaction */

$conn->begin_transaction();

try {

    /*
       First make the old schedule available again.
    */

    $stmt = $conn->prepare(
        "UPDATE schedules
         SET status = 'Available'
         WHERE id = ? AND status = 'Booked'"
    );

    $stmt->bind_param(
        "i",
        $appointment["schedule_id"]
    );

    if (!$stmt->execute()) {
        throw new Exception("Could not release the old appointment slot.");
    }

    $stmt->close();


    /*
       Book the new schedule.
    */

    $stmt = $conn->prepare(
        "UPDATE schedules
         SET status = 'Booked'
         WHERE id = ? AND status = 'Available'"
    );

    $stmt->bind_param(
        "i",
        $new_schedule_id
    );

    $stmt->execute();

    if ($stmt->affected_rows !== 1) {

        $stmt->close();

        throw new Exception(
            "The selected slot is no longer available."
        );
    }

    $stmt->close();


    /*
       Update appointment details.
    */

    $stmt = $conn->prepare(
        "UPDATE appointments
         SET
            schedule_id = ?,
            appointment_date = ?,
            appointment_time = ?
         WHERE id = ? AND patient_id = ?"
    );

    $stmt->bind_param(
        "issii",
        $new_schedule_id,
        $new_schedule["date"],
        $new_schedule["time"],
        $appointment_id,
        $patient_id
    );

    if (!$stmt->execute()) {
        throw new Exception(
            "Could not update the appointment."
        );
    }

    $stmt->close();


    /* Everything successful */

    $conn->commit();

    header("Location: appointments.php");
    exit;


} catch (Exception $e) {

    $conn->rollback();

    die(
        "Unable to reschedule the appointment. "
        . htmlspecialchars($e->getMessage())
    );
}

?>