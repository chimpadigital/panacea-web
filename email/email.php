<?php
require 'PHPMailerAutoload.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
if ($_POST)
{
    #####################################################################################################

    // Email Settings
    $site_title       = "Panacea Hotel";
    $to_email         = "sprados@chimpancedigital.com.ar";
    $default_subject  = "Panacea Hotel - Nueva consulta";

    // Output Messages
    $success_mssg   = "Mensaje enviado. Gracias.";
    $error_mssg     = "An error has occurred. Please check your PHP email configuration.";
    $short_mssg     = "Message is empty or too short! Please enter something.";
    $empty_subject  = "Subject is empty! Please enter something.";
    $empty_name     = "Name is empty! Please enter something.";
    $empty_phone    = "Phone is empty! Please enter something.";
    $email_mssg     = "Please enter a valid email!";

    //Email Text
    $tr_name    = "Name";
    $tr_email   = "Email";
    $tr_message = "Message";
    $tr_phone   = "Phone Number";

    #####################################################################################################

    //Check if its an ajax request, exit if not
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

        //Exit script outputting json data
        $output = json_encode(
        array(
            'type'=>'error',
            'text' => 'Request must come from Ajax'
        ));

        die($output);
    }
    //Sanitize input data using PHP filter_var(). *PHP 5.2.0+
    $user_name        = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
    $user_email       = filter_var($_POST["user_email"], FILTER_SANITIZE_EMAIL);
    $user_message     = filter_var($_POST["user_message"], FILTER_SANITIZE_STRING);
    $user_phone       = filter_var($_POST["user_phone"], FILTER_SANITIZE_STRING);
    // $user_subject     = filter_var($_POST["user_subject"], FILTER_SANITIZE_STRING);

    // To make a field required please remove "//"

    if (empty($user_name)){$output = json_encode(array('type'=>'error', 'text' => $empty_name)); die($output);}
    // if (empty($user_phone)){$output = json_encode(array('type'=>'error', 'text' => $empty_phone)); die($output);}
    //if (empty($user_subject)){$output = json_encode(array('type'=>'error', 'text' => $empty_subject)); die($output);}

    //Check Email
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $output = json_encode(array('type'=>'error', 'text' => $email_mssg));
        die($output);
    }

    // If Subject is empty and not rquired use default subject
    // if (empty($user_subject)) {
    //     $user_subject = $default_subject;
    // }

    //Check Message
    if (strlen($user_message) < 10 ) {
        $output = json_encode(array('type'=>'error', 'text' => $short_mssg));
        die($output);
    }

    //inicio script grabar datos en csv
    $fichero = 'panacea el hotel.csv';//nombre archivo ya creado
    //crear linea de datos separado por coma
    $fecha=date("d-m-y H:i:s");
    $linea = $fecha.";".$user_name.";".$user_phone.";".$user_email.";".$user_message."\n";
    // Escribir la linea en el fichero
    file_put_contents($fichero, $linea, FILE_APPEND | LOCK_EX);
    //fin grabar datos

    //Headers

    $email_message2 = "<h1>Detalles del formulario :</h1><br>";
    $email_message2 .= "<p>Nombre y Apellido: " . $user_name ."</p>";
    $email_message2 .= "<p>Teléfono: " . $user_phone ."</p>";
    $email_message2 .= "<p>Mail: " . $user_email ."</p>";
    $email_message2 .= "<p>Mensaje: " . $user_message ."</p>";

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 2;
    // $mail->Debugoutput = 'html';

    // $mail->Host = 'smtp.gmail.com';
    // $mail->Port = 587;
    // $mail->SMTPSecure = 'tls';
    // $mail->SMTPAuth = true;
    // $mail->Username = 'ralseffenvios@gmail.com';
    // $mail->Password = 'Ralseffenvio';
    $mail->setFrom('info@panaceahotel.com', 'Panacea Hotel');

    $mail->addReplyTo('info@panaceahotel.com','Panacea Hotel');
    $mail->addAddress('sprados@chimpancedigital.com.ar','Maria Soto');
    // $mail->addCc('ralseff@chimpancedigital.com.ar','chimpance');
    $mail->isHTML(true);
    $mail->Subject = $default_subject;
    $mail->Body    = $email_message2;
    $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
    ); 
    
    $mail->CharSet = 'UTF-8';
    //Generar log
    $date = date('d_m_Y-h_i_s_A', time());
    $log = fopen( $date.'_'.$nombre.'.txt', 'w' );
            
    $mail->Debugoutput = function($str) use ($date) {
    error_log($str, 3, __DIR__.'\\'.$date.'_'.$nombre.'.txt');
    };
            
    fclose($log);
    if (!$mail) {
        $output = json_encode(array('type'=>'error', 'text' => $error_mssg));
        die($output);
    } else {
        $output = json_encode(array('type'=>'message', 'text' => $success_mssg));
        die($output);
    }
} else {

    header('Location: ../404.html');

}
?>
