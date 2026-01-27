<?php
include_once "api-common.php";
include_once "cart-model.php";
include_once "cart-model-api-request-objects.php";
include_once "cart-model-api-response-objects.php";
include_once "validation.php";

session_start();

// ---------------------
// Handle GET requests
// ---------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['getApiResponse'])) {
        try {
            if (!isPriceOfferInSessionValid($_SESSION)) {
                $_SESSION['priceOffer'] = new PriceOffer();
                error_log("Session price offer is invalid, resetting." . json_encode($_SESSION));
            }

            /** @var PriceOffer $sessionPriceOffer */
            $sessionPriceOffer = $_SESSION['priceOffer'];
            $responsePriceOffer = PriceOffer::fromSession($sessionPriceOffer)->toResponse();

            sendJsonResponse(new ApiResponse(
                DistrictsJsonDataManipulation::getAll() ?? array(),
                $responsePriceOffer
            ), 200);
        } catch (Throwable $e) {
            error_log($e);
            sendJsonResponse(['error' => $e->getTrace()], 500);
        }
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
    sendJsonResponse($validations, 200);
}

// ---------------------
// If neither GET nor POST
// ---------------------
sendJsonResponse(['error' => 'Unsupported request method'], 405);
?>