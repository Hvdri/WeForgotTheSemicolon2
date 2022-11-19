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

//TO DO:
//->ecriptare parola+email/ecriptare toata baza de date 
//->case delogare
//->autif ong
//->autif donator

if (isset($_GET['apicall'])) {
    $json = file_get_contents('php://input');
    $obj = json_decode($json, true);

    switch ($_GET['apicall']) {
        case 'logare_donator':
            $email = $obj["email"];
            $parola = $obj["parola"];

            // Selectare în BD după e-mail și telefon
            $stmt = $conn->prepare("SELECT email, parola FROM donatori WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $randuri = $stmt->num_rows;
            $stmt->bind_result($email_donator, $parola_donator);
            $stmt->fetch();
            if ($randuri > 0 && $parola === $parola_donator && $email === $email_donator) {
                $response["eroare"] = false;
                $response["mesaj"] = "V-ati conectat cu succes!";
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "Emailul/parola este gresita!";
            }
            break;
        case 'logare_ong':
            $email = $obj["email"];
            $parola = $obj["parola"];

            // Selectare în BD după e-mail și telefon
            $stmt = $conn->prepare("SELECT email, parola FROM ong WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $randuri = $stmt->num_rows;
            $stmt->bind_result($email_ong, $parola_ong);
            $stmt->fetch();

            if ($randuri > 0 && $parola === $parola_ong && $email === $email_ong) {
                $stmt = $conn->prepare("SELECT id_ong, denumire, latitudine, longitudine, email, are_sofer, descriere FROM ong WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                $randuri = $stmt->num_rows;
                $stmt->bind_result($id_ong, $denumire, $latitudine, $longitudine, $email, $are_sofer, $descriere);
                $stmt->fetch();
                $date = array();
                $date["id_ong"] = $id_ong;
                $date["denumire"] = $denumire;
                $date["latitudine"] = $latitudine;
                $date["longitudine"] = $longitudine;
                $date["email"] = $email;
                $date["are_sofer"] = $are_sofer;
                $date["descriere"] = $descriere;
                $response["date"] = $date;
                $response["eroare"] = false;
                $response["mesaj"] = "V-ati conectat cu succes!";
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "Emailul/parola este gresita!";
            }
            break;
        case 'autentificare_donator':
            $nume = $obj["nume"];
            $prenume = $obj["prenume"];
            $numar_telefon = $obj["numar_telefon"];
            $email = $obj["email"];
            $parola = $obj["parola"];
            $stmt = $conn->prepare("SELECT id_donator FROM donatori WHERE email = ? OR numar_telefon = ?");
            $stmt->bind_param("ss", $email, $numar_telefon);
            $stmt->execute();
            $stmt->store_result();
            $randuri = $stmt->num_rows;
            if ($randuri > 0) {
                $response["eroare"] = true;
                $response["mesaj"] = "Exista deja emailul/numarul de telefon asociat unui cont!"; //schimba text
            } else {
                $stmt = $conn->prepare("INSERT INTO donatori (nume, prenume, numar_telefon, email, parola) VALUES (?), (?), (?), (?), (?)");
                $stmt->bind_param("sssss", $nume, $prenume, $numar_telefon, $email, $parola);
                $stmt->execute();
                $response["eroare"] = false;
                $response["mesaj"] = "Autentificarea a fost facuta cu succes";
            }
            break;
        case 'autentificare_ONG':
            $cif = $obj["cif"];
            $denumire_ong = $obj["denumire_ong"];
            $adresa = $obj["adresa"];
            $email = $obj["email"];
            $parola = $obj["parola"];
            $are_sofer = $obj["are_sofer"];
            $descriere = $obj["descriere"];
            $numar_telefon = $obj["numar_telefon"];
            $stmt = $conn->prepare("SELECT id_ong FROM ong WHERE cif = ? ");
            $stmt->bind_param("s", $cif);
            $stmt->execute();
            $stmt->store_result();
            $randuri = $stmt->num_rows;
            if ($randuri > 0) {
                $response["eroare"] = true;
                $response["mesaj"] = "Exista deja ONG-ul asociat unui cont!"; //schimba text
            } else {
                $stmt = $conn->prepare("INSERT INTO ong (cif, denumire_ong, adresa, email, parola, are_sofer, descriere, numar_telefon) VALUES (?), (?), (?), (?), (?), (?), (?), (?)");
                $stmt->bind_param("ssssssss", $cif, $denumire_ong, $adresa, $email, $parola, $are_sofer, $descriere, $numar_telefon);
                $stmt->execute();
                $response["eroare"] = false;
                $response["mesaj"] = "Autentificarea a fost facuta cu succes";
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
