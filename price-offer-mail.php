<?php

include_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require "vendor/autoload.php";

/**
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendMailWithExcelAttachment(string $excelSpreadsheet, PriceOfferResponse $priceOffer): void
{
    $appConfig = AppConfigJsonDataManipulation::getAll();
    $email = $appConfig["isProductionSmtp"] ? getMailConfigProd($appConfig) : getMailConfigLocalhost();

    try {
        $email->isHTML();
        $email->setFrom($appConfig["mailFrom"], $appConfig["mailFromName"]);
        $email->addCC($appConfig["mailCc"]);

        $mailAddressTo = $priceOffer->contact->email;
        $email->addAddress($mailAddressTo);

        $email->addEmbeddedImage(
            __DIR__ . '/assets/img/mail.jpg',
            'mail_image',
            'logo.jpg',
            'base64',
            'image/jpeg'
        );

        $priceOfferSequentialId = str_pad(queryPriceOfferNumber(getDb()), 3, '0', STR_PAD_LEFT);
        $email->Subject = '💥 STOLÁRSTVO SUČANSKÝ 💥 - CENOVÁ PONUKA - 🚪 INTERIÉROVÉ DVERE 🚪 ČÍSLO - ' . date('y-m') . "-" . $priceOfferSequentialId;
        $email->Body = file_get_contents(__DIR__ . '/price-offer-mail.html');
        $email->addStringAttachment(
            $excelSpreadsheet,
            'cenova_ponuka.xlsx',
            'base64',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $email->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $email->ErrorInfo);
        error_log("Exception: " . $e);
        throw $e;
    }
}

function getMailConfigLocalhost(): PHPMailer
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->Host = '127.0.0.1';
    $mail->Port = 25;
    $mail->SMTPAuth = false;
    $mail->SMTPSecure = false;

    return $mail;
}

function getMailConfigProd(array $appConfig): PHPMailer
{
    $mail = new PHPMailer(true);

    if ($appConfig["mailSmtpDebugMode"]) {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    }

    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->Host = $appConfig["mailSmtpHost"];
    $mail->SMTPAuth = true;
    $mail->Username = $appConfig["mailSmtpUsername"];
    $mail->Password = $appConfig["mailSmtpPassword"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    return $mail;
}