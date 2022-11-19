<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set("mail.log", "/tmp/mail.log");
ini_set("mail.add_x_header", TRUE);
date_default_timezone_set('Europe/Bucharest');
require_once 'conectare.php';

$response = array();

if (isset($_GET['apicall'])) {
    $json = file_get_contents('php://input');
    $obj = json_decode($json, true);

    switch ($_GET['apicall']) {
        case 'logare':
            $email = $obj["email"];
            $parola = $obj["parola"];

            // Selectare în BD după e-mail și telefon
            $stmt = $conn->prepare("SELECT email, parola FROM angajati WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $randuri = $stmt->num_rows;
            $stmt->bind_result($email_BD, $parola_BD);
            $stmt->fetch();

            if ($randuri > 0 && $parola === $parola_BD && $email === $email_BD) {
                $response["eroare"] = false;
                $response["mesaj"] = "V-ati conectat cu succes!";
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "Emailul/parola este gresita!";
            }
            break;
        default:
            $response["eroare"] = true;
            $response["mesaj"] = "A aparut o eroare";
    }
} else {
    $response["eroare"] = true;
    $response["mesaj"] = "A aparut o eroare";
}

echo json_encode($response);
