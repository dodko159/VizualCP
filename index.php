<?php
include_once "cart-model.php";
include_once "json-data-manipulation.php";

session_start();
//session_destroy();
//Require https
/*
if ($_SERVER['HTTPS'] != "on"){
    $url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    header("Location: $url");
}*/
?>

<html lang="sk">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Visualizer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
          content="Prezrite si našu VIZUALIZÁCIU interiérových dverí, kde nájdete viac ako 100 druhov dverí v 40 rôznych farebných dekoroch, usporiadaných v 8 prehľadných kategóriách."/>
    <link rel="stylesheet"
          href="<?php echo AppConfigJsonDataManipulation::getAll()["baseUrl"] . "/public/assets/main.css" ?>">

    <?php
    include_once "constants.php";
    include "_import.php";
    include_once "functions.php";
    ?>
</head>

<body>
<?php

include "mobile_alert.php";
include "header.php";
include "content.php";
include "cart.php";
include "cart-new.php";
//include "footer.php";
?>
</body>
</html>