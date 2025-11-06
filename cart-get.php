<?php
include_once "cart-class.php";
session_start();

$result[] = array(
    'sucess' => false,
    'message' => "Bad call.",
    'data'=> null
);

if( isset($_GET['price']) )
{
    $result['message'] = "Insufficient data.";

    try {
        $PO = fixObject($_SESSION['priceOffer']);
        $price = $PO->getPriceOf($_GET['price']);
        $result = array(
            'sucess' => true,
            'message' => "OK",
            'data'=> $price
        );
    }
    catch(Exception $e){
        $result['message'] = $e->getMessage();
    }

}

if( isset($_GET['fullPrice']) )
{
    $result['message'] = "Insufficient data.";

    try {
        $PO = fixObject($_SESSION['priceOffer']);
        $price = $PO->getFullPrice();
        $result = array(
            'sucess' => true,
            'message' => "OK",
            'data'=> $price
        );
    }
    catch(Exception $e){
        $result['message'] = $e->getMessage();
    }

}

echo json_encode($result);

?>