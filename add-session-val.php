<?php
include_once "cart-class.php";
session_start();


$result[] = array(
    'sucess' => true,
    'message' => "OK",
    'result'=> "OK"
);

//contact info
if( isset($_POST['name']) )
{
    $_SESSION["name"] = $_POST["name"]; 
}
if( isset($_POST['mail']) )
{
    $_SESSION["mail"] = $_POST["mail"]; 
}
if( isset($_POST['phone']) )
{
    $_SESSION["phone"] = $_POST["phone"]; 
}

//actual door
if( isset($_POST['doorType']) )
{
    $_SESSION["doorType"] = $_POST["doorType"]; 
}
if( isset($_POST['material']) )
{
    $_SESSION["material"] = $_POST["material"]; 
}

//functions
if( isset($_POST['addDoor']) )
{

    try{
        if (!array_key_exists('priceOffer', $_SESSION)) {
            $_SESSION['priceOffer'] = new CP();
        }
    
        $PO = fixObject($_SESSION['priceOffer']);
        $result = $PO->addDoor(
            new Door(
                getCategoryFromDoorType($_POST["doorType"]), //kategoria
                $_POST["doorType"], //typ
                $_POST["material"], //material
                Width::getWidthFromString($_POST["width"]), //sirka
                $_POST["count"], //pocet
                $_POST["info"],
                $_POST["frame"],
                $_POST["assembly"]) 
        );
    }
    catch(Exception $e){
        $result['sucess'] = false;
        $result['message'] = "Chyba pri vkladaní";
        $result['result'] = $e->getMessage();
    }
    
}

if( isset($_POST['function']) )
{
    try{
        $PO = fixObject($_SESSION['priceOffer']);
        $result = $PO->doPostFunction($_POST);
    }
    catch(Exception $e){
        $result['sucess'] = false;
        $result['message'] = "Chyba volania";
        $result['result'] = $e->getMessage();
    }
    
}

echo json_encode($result);

?>