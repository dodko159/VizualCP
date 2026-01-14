<?php
include_once "api-common.php";
include_once "cart-model.php";
include_once "json-data-manipulation.php";
include_once "price-offer-spreadsheet.php";
include_once "price-offer-mail.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);

    if (!AppConfigJsonDataManipulation::getAll()["reCaptchaEnabled"] || verifyRecaptcha($requestBody["g-recaptcha-response"])) {
        /** @var PriceOfferResponse $priceOffer */
        $priceOffer = $_SESSION["priceOffer"]->toResponse();
        $spreadsheet = generateSpreadSheet($priceOffer);

        try {
            sendMailWithExcelAttachment($spreadsheet, $priceOffer);
        } catch (Exception $e) {
            sendJsonResponse(["error" => "ERROR_SENDING_MAIL"], 500);
        }

        sendJsonResponse(array(), 200);
    } else {
        sendJsonResponse(["error" => "RECAPTCHA_FAILED"], 400);
    }
}

sendJsonResponse(["error" => "Unsupported request method."], 405);

function verifyRecaptcha($token): bool
{
    $secretKey = AppConfigJsonDataManipulation::getAll()["reCaptchaSecretKey"];
    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token"
    );
    $result = json_decode($verify);
    return boolval($result->success);
}

?>