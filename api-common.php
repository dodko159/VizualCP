<?php
function sendJsonResponse($data, int $statusCode)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}