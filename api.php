<?php
include_once "api-common.php";
include_once "cart-model.php";
include_once "validation.php";

session_start();

// ---------------------
// Handle GET requests
// ---------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['getApiResponse'])) {
        if (!isPriceOfferInSessionValid($_SESSION)) {
            unset($_SESSION['priceOffer']);
            sendJsonResponse(['error' => 'No price offer in session'], 404);
        }

        /** @var PriceOffer $sessionPriceOffer */
        $sessionPriceOffer = $_SESSION['priceOffer'];
        $responsePriceOffer = $sessionPriceOffer->toResponse();

        sendJsonResponse(new ApiResponse(
            DistrictsJsonDataManipulation::getAll() ?? array(),
            $responsePriceOffer
        ), 200);
    } else {
        sendJsonResponse(['error' => 'Missing getApiResponse parameter'], 400);
    }
}

// ---------------------
// Handle POST requests (JSON body)
// ---------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['error' => 'Invalid JSON'], 400);
    }

    $parsedObject = new ApiRequest($requestBody);
    $priceOffer = PriceOffer::fromRequest($parsedObject->priceOffer);
    $_SESSION['priceOffer'] = $priceOffer;

    $validations = validate($parsedObject);
    if (!empty($validations)) {
        sendJsonResponse($validations, 400);
    } else {
        sendJsonResponse($priceOffer, 200);
    }
}

// ---------------------
// If neither GET nor POST
// ---------------------
sendJsonResponse(['error' => 'Unsupported request method'], 405);
?>