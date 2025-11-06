<?php
include_once "cart-class.php";
include_once "generatePDF.php";
session_start();
include_once "constants.php";


//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

// build HTML
$errMsg = "Error sending message";
$retval1 = false;
$retval2 = false;
$canSend = false;

try {
   // data sent in header are in JSON format
   header('Content-Type: application/json');
   // takes the value from variables and Post the data
   $name = $_POST['name'];
   $email = $_POST['email'];
   $contact = $_POST['contact'];

   $doorInfo = null;
   if (isset($_SESSION['priceOffer'])) {
      $PO = fixObject($_SESSION['priceOffer']);
      if(isset($PO) && isset($PO->doors)){
         $canSend = true;
         $doorInfo = "<table style=\"border-collapse: collapse;\"><tr>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\"></th>  
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Šírka</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Typ</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Povrchová úprava</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Počet</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Zárubňa</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Montáž</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Cena za kus*</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Cena celkom**</th>
           <th style=\"border: 1px solid #dddddd; padding: 5px;\">Poznámka</th>
         </tr>";
         $idx = 1;
         foreach($PO->doors as $door){
            $background = "";
            if($idx % 2 == 1){
               $background = ' style="background-color: #dddddd;"';
            }
            $zaruben = "Nie";
            if($door->frame){
               $zaruben = "Áno";
            }
            $montaz = "Nie";
            if($door->assembly){
               $montaz = "Áno";
            }
            $doorInfo .= "<tr$background><td style=\"border: 1px solid #dddddd; padding: 5px;\">$idx</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".$door->getWidthString()."</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".strtoupper($door->type)."</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".getMaterialNameByKey($door->material)."</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$door->count</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$zaruben</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$montaz</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".$door->price.$currency."</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".$door->getFullPrice().$currency."</td>";
            $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$door->info</td></tr>";
            $idx++;
         }

         $doorInfo .= "<tr><td colspan=\"8\" style=\"border: 0px; padding: 5px;\"><b>Spolu</b></td><td colspan=\"2\" style=\"border: 0px; padding: 5px;\"><b>".$PO->getFullPriceNoAdd().$currency."</b></td></tr>";
         $doorInfo .= "</table>";
         $doorInfo .= "*cena bez zárubne bez DPH<br>";
         $doorInfo .= "**cena celkom so zárubňov (ak je zvolená) bez DPH<br><br>";
         $doorInfo .= "<b>Dodatočné služby</b><br>";
         //$doorInfo .= "Montáž: ".$PO->getAssemblyPrice().$currency."<br>";
         $doorInfo .= "Obložky bez dverí: ".$PO->getDoorLiners()."ks - ".$PO->getLinerPrice().$currency."<br>";
         $doorInfo .= "Vytmelenie nerovností: ".$PO->getPuttyPrice().$currency."<br>";
         $doorInfo .= "Silikónové tesnenie: ".$PO->getSealPrice().$currency."<br>";
         $doorInfo .= "Obklad oceľových zárubní: ".$PO->getIronPrice().$currency."<br>";
         $doorInfo .= "Vynášanie na 3. poschodie a viac: ".$PO->getFloor3Price().$currency."<br>";

         $doorInfo .= "Príplatok za hrubšie obložky: ".$PO->getThickerFramePrice().$currency."<br>";
         $doorInfo .= "Príplatok za vysoké dvere: ".$PO->getHigherFramePrice().$currency."<br>";
         $doorInfo .= "Doprava: ".$PO->getDistance()."km - ".$PO->getDistancePrice().$currency."<br>";

         $doorInfo .= "<b>Celková cena: ".$PO->getFullPrice().$currency."</b><br>";
      }
   }

   // Email Template
   $message = "<b>Meno: </b>". $name ."<br>";
   $message .= "<b>E-mail: </b>".$email."<br>";
   $message .= "<b>Tel. č.: </b>".$contact."<br>";
   if ($doorInfo != null) {
      $message .= "<br>".$doorInfo."<br>";
   }
/*
   $header1 = "From: ".$name."<".$email."> \r\n";
   $header1 .= "MIME-Version: 1.0\r\n";
   $header1 .= "Content-type: text/html\r\n";

   $header2 = "From: Sučanský<".$to."> \r\n";
   $header2 .= "MIME-Version: 1.0\r\n";
   $header2 .= "Content-type: text/html\r\n";

   if($canSend) {
      $retval1 = mail ($to,$subject,$message,$header1); // do stolarstva
      $retval2 = mail ($email,$subject,$message,$header2); // zakaznikovi
   }*/
} catch(Exception $e){
   $errMsg = $e->getMessage();
}

if ($canSend) {
   //Create an instance; passing `true` enables exceptions
   $mail = new PHPMailer(true);

   try {
      //Server settings
      //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
      $mail->isSMTP();                                            //Send using SMTP
      $mail->CharSet    = "UTF-8";
      $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = 'janicekd0@gmail.com';                     //SMTP username
      $mail->Password   = 'kvyj wbne nlkj hsxk';                               //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
      $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //Recipients
      $mail->setFrom($email, $name);
      $mail->addAddress('janciekd0@gmail.com');     //Add a recipient
      //$mail->addReplyTo('info@example.com', 'Information');
      //$mail->addCC($email);
      //$mail->addBCC('bcc@example.com');

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

      $pdf = generatCPPDF();
      /*if ($pdf != null) {
         $outPdf = $pdf->output();
         $mail->addAttachment($outPdf, 'application/pdf','cenova_ponuka.pdf', false);
      }*/

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = "Cenová ponuka - " . $name;
      $mail->Body    = $message;

      //$mail->send();
      
      //echo 'Message has been sent';
      /*echo json_encode(array(
         'success'=> true,
         'message' => 'Message sent successfully'
      ));*/
      $outPdf = $pdf->output();
      /*echo json_encode(array(
         'success'=> true,
         'message' => "done"
      ));*/
      echo $outPdf;
   } catch (Exception $e) {
      //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      echo json_encode(array(
         'success'=> false,
         'message' => "{$mail->ErrorInfo}"
      ));
   }
} else {
   echo json_encode(array(
      'success'=> false,
      'message' => "{$errMsg}"
   ));
}