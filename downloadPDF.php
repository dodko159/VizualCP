<?php
include "generatePDF.php";
session_start();

try {
    $pdf = generatCPPDF();
    $pdf -> stream("cenova_ponuka.pdf", array("Attachment" => 1)); // Stiahnutie
    //$pdf -> output();
/*
    echo json_encode(array(
        'success'=> true,
        'pdf' => $pdf -> stream("cenova_ponuka.pdf"),
        'message' => "OK"
     ));*/
} catch(Exception $e) {
    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    /*echo json_encode(array(
       'success'=> false,
       'message' => $e->getMessage()
    ));*/
 }