<?php
include_once "cart-model.php";
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
        /** @var PriceOffer $priceOffer */
        $priceOffer = fixObject($_SESSION['priceOffer']);
        $price = $priceOffer->getPriceOf($_GET['price']);
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
        $priceOffer = fixObject($_SESSION['priceOffer']);
        $price = $priceOffer->getFullPrice();
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