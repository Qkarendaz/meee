<?php

$msg = "<!DOCTYPE html>

        <html>
          <head>
            <style>
              .container{
                margin: 20px;
                width: 100%;
                background: lightgrey;
              }
              .container div{
                width: 60%;
                background: #fff;
                text-align: left;
                padding: 30px;
              }
              button {
                background-color: #008CBA; /* Green */
                border: none;
                border-radius: 5px;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
              }
            </style>
          <head>
          <body>
            <center>
              <div class='container'>
                <img src='https://www.pngfind.com/pngs/m/2-23180_instagram-1-logo-png-transparent-instagram-name-logo.png' width='15%'>
                <div>
                  <p><strong>Hi $firstname $lastname,</strong><br>Welcome to Expovids!</p><br>
                  <div style='border:0.5px solid lightgrey;padding:0px;width:100%;'></div><br>
                  <p>
                    Click on the button below to confirm that $email is your valid email!<br><br><br>
                    <center>
                      <a href='http://tutme.ml/register/verify.php?vkey='.$vkey.''>
                        <button>Confirm Email</button>
                      </a>
                    </center><br><br><br>
                    Once you do, you will gain full access to all the features of yor account on the website.
                    <br><br>
                    Learn big,<br>
                    Team Expovids.
                  </p>
                </div>
                <img src='https://www.pngfind.com/pngs/m/2-23180_instagram-1-logo-png-transparent-instagram-name-logo.png' width=15%'>
              </div>
            </center>
          </body>
        </html>';

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Base files 
//require 'PHPMailer/src/Exception.php';
//require 'PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/SMTP.php';

// create object of PHPMailer class with boolean parameter which sets/unsets exception.

$mail = new PHPMailer(true);                              

try {

    //server settings...
    //$mail->SMTPDebug = 2;
    $mail->isSMTP(); // using SMTP protocol     
                                
    $mail->Host = 'smtp.gmail.com'; // SMTP host as gmail 
    $mail->SMTPAuth = true;  // enable smtp authentication                             
    $mail->Username = 'xoxoxoxo';  // sender email             
    $mail->Password = 'xoxoxo'; // sender email  password                          
    $mail->SMTPSecure = 'tls';  // for encrypted connection                           
    $mail->Port = 587;   // port for SMTP     465
    $mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
    );

    //recipients..
    $mail->setFrom("no-reply@tutme.ml", "Tutme"); // sender's email and name
    $mail->addAddress($email);  // receiver's email and name
    //$mail->addReplyTo('qkarendaz4real@gmail.com');
    //$mail->addCC('your@email.com');
    //$mail->addBCC('bbcyour@email.com'); // if add email recipeints will not no that a copy is send

    //contents..
    $mail->isHTML(true);
    $mail->Subject = 'Tutme Account Verification';
    $mail->Body    = $msg;
    //convert HTML into a basic plain-text alternative body..
    //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
    //$mail->AltBody    = "Every thing is possible in this world"; // for non-html mail clients.
    
    // Attachments
   // $mail->addAttachment("img.jpg);
    // $mail->addAttachment("img2.jpg, kkk.jpg);
    $mail->send();

    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $mysqli->query($sql);
    
	if ($row = $result->fetch_assoc()) {
		$_SESSION['id'] = $row['id'];
		$_SESSION['image'] = $row['prof_image'];
        $_SESSION['firstname'] = $row['firstname'];
		$_SESSION['lastname'] = $row['lastname'];
		$_SESSION['email'] = $row['email'];
        $_SESSION['cat1'] = $row['cat1'];
        $_SESSION['cat2'] = $row['cat2'];
        $_SESSION['cat3'] = $row['cat3'];
        $_SESSION['cat4'] = $row['cat4'];
        $_SESSION['cat5'] = $row['cat5'];
	}

	header("Location: thankyou.php");
}catch (Exception $e)
 {
  // handle error.
    echo "having some issues sending mail. Mailer Error: {$mail->ErrorInfo}";
}
?>
