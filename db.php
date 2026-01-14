<?php
function getDb(): SQLite3
{
    error_log(AppConfigJsonDataManipulation::getAll()['dbFilename']);
    $db = new SQLite3(AppConfigJsonDataManipulation::getAll()['dbFilename']);
    $db->exec("
    CREATE TABLE IF NOT EXISTS price_offer_number (
        id INTEGER PRIMARY KEY CHECK (id = 1),
        value INTEGER NOT NULL
    )
");

    $db->exec("
    INSERT OR IGNORE INTO price_offer_number (id, value)
    VALUES (1, 0)
");

    return $db;
}

function queryPriceOfferNumber(SQLite3 $db): int
{
    $db->exec("BEGIN IMMEDIATE");

    $db->exec("
        UPDATE price_offer_number
        SET value = value + 1
        WHERE id = 1
    ");

    $newValue = $db->querySingle(
            "SELECT value FROM price_offer_number WHERE id = 1"
    );

    $db->exec("COMMIT");

    return (int)$newValue;
}