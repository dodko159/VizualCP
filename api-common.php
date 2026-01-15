<?php
include_once "cart-model.php";

function sendJsonResponse($data, int $statusCode)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function isPriceOfferInSessionValid(array $session): bool
{
    return !(isset($session['priceOffer']) && $session['priceOffer'] instanceof __PHP_Incomplete_Class);
}