<?php
include_once "api-common.php";
include_once "cart-model.php";
include_once "validation.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appConfig = AppConfigJsonDataManipulation::getAll();

    sendJsonResponse(
        new AppConfigResponse(
            boolval($appConfig["reCaptchaEnabled"]),
            $appConfig["reCaptchaSiteKey"]
        ),
        200
    );
}

sendJsonResponse(['error' => 'Unsupported request method'], 405);
?>