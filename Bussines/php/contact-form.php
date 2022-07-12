<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require $_SERVER['DOCUMENT_ROOT'] . "/armargaSoluciones/vendor/autoload.php";
// require __DIR__.'/PHPMailer/PHPMailer.php';


$SECRET_KEY = "6Lcq5eIgAAAAAFj--sNdXsfwEj6pjQqRXmrgN1Cz";

$errorMSG = "";

if (empty($_POST["name"])) {
    $errorMSG = "Name is required ";
} else {
    $name = $_POST["name"];
}

if (empty($_POST["email"])) {
    $errorMSG = "Email is required ";
} else {
    $temail = strval($_POST["email"]);
}

if (empty($_POST["phone"])) {
    $errorMSG = "Phone is required ";
} else {
    $phone = $_POST["phone"];
}

if (empty($_POST["terms"])) {
    $errorMSG = "Terms is required ";
} else {
    $terms = $_POST["terms"];
}
if (empty($_POST["descripcion"])) {
    $errorMSG = "Descripcion is required ";
} else {
    $descripcion = $_POST["descripcion"];
}

if (empty($_POST["tokengoogle"])) {
    $errorMSG = "tokengoogle is required ";
} else {
    $tokengoogle = $_POST["tokengoogle"];
}


$respuestaValidacion = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $SECRET_KEY . "&response={$tokengoogle}");

$jsonResponde = json_decode($respuestaValidacion);
$jsonResponde = (array) $jsonResponde;
if ($jsonResponde['success'] && ($jsonResponde['score'] && $jsonResponde['score'] > 0.5)) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.hostinger.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'info@armargasoluciones.com';                     //SMTP username
        $mail->Password   = 'Colombia2022#';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->CharSet = "utf-8"; // set charset to utf8

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        //Recipients
        $mail->setFrom("info@armargasoluciones.com", "Notificación Armar Gas Soluciones");
        $mail->addAddress("servicioalcliente@armargasoluciones.com");
        // $mail->addAddress('ellen@example.com');               //Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC($temail);
        $mail->addBCC('jgas-09@hotmail.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $body = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Notificación de mensaje</title></head><body>";
        $body .= "<h3>¡Hola! {$name}</h3><br/>";
        $body .= "<p>Nos complace que te hayas comunicado con nosotros, en breve unos de nuestros asesores se comunicará contigo.</p><br/>";
        $body .= "<p>Datos de Contacto: </p><br/>";
        $body .= "<table style='width: 100%;'>";
        $body .= "<thead style='text-align: center;'><tr><td style='border:none;' colspan='1'>";
        $body .= "</td></tr></thead><tbody><tr>";
        $body .= "<td style='border:none;'><strong>Nombre del Cliente:</strong> {$name}</td>";
        $body .= "<td style='border:none;'><strong>Correo Electrónico:</strong> {$temail}</td>";
        $body .= "<td style='border:none;'><strong>Telefono:</strong> {$phone}</td>";
        $body .= "</tr>";
        $body .= "<tr><td></td></tr>";
        $body .= "<tr><td colspan='2' style='border:none;'><strong>Mensaje: </strong>{$descripcion}</td></tr>";
        $body .= "</tbody></table><br/><br/><br/><br/>";
        $body .= "<small><i>Por favor no responder este correo, ya que fue enviado de manera automatica por el sistema de Armar Gas Soluciones.</i></small>";
        $body .= "</body></html>";


        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = '¡Gracias! por comunicarse con nosotros';
        $mail->Body    =  $body;
        $mail->AltBody = $body;

        $mail->send();
        $output = json_encode(array('status' => true, 'type' => 'message', 'text' => 'Hola, gracias por contactarse con nosotros!'));
        die($output);
    } catch (\Exception $e) {
        $output = json_encode(array('status' => false, 'type' => 'error', 'text' => 'No fue posible enviar el mensaje, intenta mas tarde! - ' . $e->getMessage()));
        die($output);
    }
} else {
    //Google ha detectado que se trata de un proceso no humano
    $output = json_encode(array('status' => false, 'type' => 'error', 'text' => 'Validacion de Captcha Incorrecta, recarga la pagina para volver a intentar!'));
    die($output);
}
