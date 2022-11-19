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

//tip_haine: bebelusi, copii, adulti
//TO DO:
//->ecriptare parola+email/ecriptare toata baza de date 

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
                $stmt = $conn->prepare("SELECT id_donator, nume, prenume, numar_telefon, adrese, email FROM donatori WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                $randuri = $stmt->num_rows;
                $stmt->bind_result($id_donator, $nume, $prenume, $numar_telefon, $adrese, $email);
                $stmt->fetch();
                $date = array();

                $date["id_donator"] = $id_donator;
                $date["nume"] = $nume;
                $date["prenume"] = $prenume;
                $date["numar_telefon"] = $numar_telefon;
                $date["adrese"] = $adrese;
                $date["email"] = $email;

                $response["date"] = $date;
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
                $stmt = $conn->prepare("SELECT id_ong, denumire_ong, email, are_sofer, descriere, adresa, numar_telefon, cif, imagine FROM ong WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                $randuri = $stmt->num_rows;
                $stmt->bind_result($id_ong, $denumire_ong, $email, $are_sofer, $descriere, $adresa, $numar_telefon, $cif, $imagine);
                $stmt->fetch();
                $date = array();

                $date["id_ong"] = $id_ong;
                $date["denumire_ong"] = $denumire_ong;
                $date["email"] = $email;
                $date["are_sofer"] = $are_sofer;
                $date["descriere"] = $descriere;
                $date["adresa"] = $adresa;
                $date["imagine"] = $imagine;
                $date["numar_telefon"] = $numar_telefon;
                $date["cif"] = $cif;

                $response["date"] = $date;
                $response["eroare"] = false;
                $response["mesaj"] = "V-ati conectat cu succes!";
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "Emailul/parola este gresita!";
            }
            break;

            //VERIFICAT
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
                $stmt = $conn->prepare("INSERT INTO donatori (nume, prenume, numar_telefon, email, parola) VALUES (?, ?, ?, ?, ?)");
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
            $imagine = $obj["imagine"];

            $stmt = $conn->prepare("SELECT id_ong FROM ong WHERE cif = ? ");
            $stmt->bind_param("s", $cif);
            $stmt->execute();
            $stmt->store_result();
            $randuri = $stmt->num_rows;

            if ($randuri > 0) {
                $response["eroare"] = true;
                $response["mesaj"] = "Exista deja ONG-ul asociat unui cont!"; //schimba text
            } else {
                $stmt = $conn->prepare("INSERT INTO ong (cif, denumire_ong, adresa, email, parola, are_sofer, descriere, numar_telefon, imagine) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", $cif, $denumire_ong, $adresa, $email, $parola, $are_sofer, $descriere, $numar_telefon, $imagine);
                $stmt->execute();

                $response["eroare"] = false;
                $response["mesaj"] = "Autentificarea a fost facuta cu succes";
            }
            break;

        case 'ong_cereri_haine':
            $tip_haine = $obj["tip_haine"];
            $id_ong = $obj["id_ong"];
            $cantitate = $obj["cantitate"];
            $mesaj = $obj["mesaj"];

            $stmt = $conn->prepare("INSERT INTO ong_cereri_haine (tip_haine, id_ong, cantitate, mesaj) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $tip_haine, $id_ong, $cantitate, $mesaj);
            $stmt->execute();

            $response["eroare"] = false;
            $response["mesaj"] = "Cererea a fost creata cu succes!";
            break;

        case 'ong_cerere_jucarii':
            $id_ong = $obj["id_ong"];
            $cantitate = $obj["cantitate"];
            $mesaj = $obj["mesaj"];

            $stmt = $conn->prepare("INSERT INTO ong_cereri_jucarii (id_ong, cantitate, mesaj) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $id_ong, $cantitate, $mesaj);
            $stmt->execute();

            $response["eroare"] = false;
            $response["mesaj"] = "Cererea a fost creata cu succes!";
            break;

        case 'donatii_haine':
            $id_donator = $obj["id_donator"];
            $tip_haine = $obj["tip_haine"];
            $cantitate = $obj["cantitate"];
            $adresa = $obj["adresa"];
            $data_donatie = $obj["data_donatie"];
            $disponibilitate_zi = $obj["disponibilitate_zi"];

            $stmt = $conn->prepare("SELECT id_ong, cantitate FROM ong_cereri_haine WHERE tip_haine = ? AND cantitate > 0");
            $stmt->bind_param("s", $tip_haine);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!empty($result)) {
                $onguri_ok_haine = array();
                while ($row = $result->fetch_assoc()) {
                    $stmt = $conn->prepare("SELECT denumire_ong, imagine FROM ong WHERE id_ong = ?");
                    $stmt->bind_param("s", $row["id_ong"]);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($denumire_ong, $imagine);
                    $stmt->fetch();

                    $ong_ok_haine = array();
                    $ong_ok_haine["id_ong"] = $row["id_ong"];
                    $ong_ok_haine["denumire_ong"] = $denumire_ong;
                    $ong_ok_haine["imagine"] = $imagine;

                    array_push($onguri_ok_haine, $ong_ok_haine);
                }


                $stmt = $conn->prepare("INSERT INTO donatii_haine (id_donator, tip_haine, cantitate, adresa, data_donatie, disponibilitate_zi) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $id_donator, $tip_haine, $cantitate, $adresa, $data_donatie, $disponibilitate_zi);
                $stmt->execute();

                $stmt = $conn->prepare("SELECT MAX(id_donatie_haine) FROM donatii_haine WHERE id_donator = ?");
                $stmt->bind_param("s", $id_donator);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($id_donatie_haine);
                $stmt->fetch();

                $response["eroare"] = false;
                $response["mesaj"] = "ONG-urile au fost trimise cu succes!";
                $response["onguri"] = $onguri_ok_haine;
                $response["id_donatie_haine"] = $id_donatie_haine;
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "Nu exista cereri pentru acest tip de donatie!";
            }

            break;

        case 'donatii_jucarii':
            $id_donator = $obj["id_donator"];
            $id_ong = $obj["id_ong"];
            $cantitate = $obj["cantitate"];
            $data_donatie = $obj["data_donatie"];
            $disponibilitate_zi = $obj["disponibilitate_zi"];

            $stmt = $conn->prepare("INSERT INTO donatii_jucarii (id_donator, id_ong, cantitate, data_donatie, disponibilitate_zi) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $id_donator, $id_ong, $cantitate, $data_donatie, $disponibilitate_zi);
            $stmt->execute();

            $stmt = $conn->prepare("SELECT MAX(id_donatie_jucarii) FROM donatii_jucarii WHERE id_donator = ?");
            $stmt->bind_param("s", $id_donator);
            $stmt->execute();
            $stmt->store_result();
            $randuri = $stmt->num_rows;
            $stmt->bind_result($id_donatie_jucarii);
            $stmt->fetch();

            if ($randuri > 0) {
                $stmt = $conn->prepare("SELECT id_ong, cantitate FROM ong_cerere_jucarii WHERE cantitate > 0");
                $stmt->execute();
                //$stmt->store_result();
                $randuri = $stmt->num_rows;

                if ($randuri > 0) {
                    $onguri_ok_jucarii = array();
                    while ($row = $stmt->fetch()) {
                        $stmt = $conn->prepare("SELECT denumire_ong, imagine FROM ong WHERE id_ong = ?");
                        $stmt->bind_param("s", $row["id_ong"]);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($denumire_ong, $imagine);
                        $stmt->fetch();

                        $ong_ok_jucarii = array();
                        $ong_ok_jucarii["id_ong"] = $row["id_ong"];
                        $ong_ok_jucarii["denumire_ong"] = $denumire_ong;
                        $ong_ok_jucarii["imagine"] = $imagine;

                        array_push($onguri_ok_jucarii, $ong_ok_jucarii);
                    }
                    $response["eroare"] = false;
                    $response["mesaj"] = "ONG-urile au fost trimise cu succes!";
                    $response["onguri"] = $onguri_ok_jucarii;
                    $response["id_donatie_haine"] = $id_donatie_jucarii;
                } else {
                    $response["eroare"] = true;
                    $response["mesaj"] = "A aparut o eroare la trimiterea ONG-urilor!";
                }
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "A aparut o eroare la crearea jucarii!";
            }
            break;

        case 'ong_ok_haine':
            $id_ong = $obj("id_ong");
            $id_donatie_haine = $obj("id_donatie_haine");

            $stmt = $conn->prepare("UPDATE donatii_haine SET id_ong = ? WHERE id_donatie_haine = ? ");
            $stmt->bind_param("ss", $id_ong, $id_donatie_haine);
            $stmt->execute();

            $stmt = $conn->prepare("SELECT id_ong FROM ong WHERE id_donatie_haine = ?");
            $stmt->bind_param("s", $id_donatie_haine);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id_ong_upd);
            $stmt->fetch();

            if ($id_ong === $id_ong_upd) {
                $response["eroare"] = false;
                $response["mesaj"] = "ONG ales cu succes!";
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "A aparut o eroare la alegerea ONG-ului!";
            }
            break;

        case 'ong_ok_jucarii':
            $id_ong = $obj("id_ong");
            $id_donatie_jucarii = $obj("id_donatie_jucarii");

            $stmt = $conn->prepare("UPDATE donatii_jucarii SET id_ong = ? WHERE id_donatie_jucarii = ? ");
            $stmt->bind_param("ss", $id_ong, $id_donatie_jucarii);
            $stmt->execute();

            $stmt = $conn->prepare("SELECT id_ong FROM ong WHERE id_donatie_jucarii = ?");
            $stmt->bind_param("s", $id_donatie_jucarii);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id_ong_upd);
            $stmt->fetch();

            if ($id_ong === $id_ong_upd) {
                $response["eroare"] = false;
                $response["mesaj"] = "ONG ales cu succes!";
            } else {
                $response["eroare"] = true;
                $response["mesaj"] = "A aparut o eroare la alegerea ONG-ului!";
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
