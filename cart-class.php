<?php

include_once('constants.php');
include_once('functions.php');

abstract class Width {
    const W60 = 1;
    const W70 = 2;
    const W80 = 3;
    const W90 = 4;

    public static function getWidthFromString($sWidth) {
        switch($sWidth){
            case "W60":
                return Width::W60;
            case "W70":
                return Width::W70;
            case "W80":
                return Width::W80;
            case "W90":
                return Width::W90;
        }
    }

    public static function getWidthString($width) {
        switch($width){
            case Width::W60:
                return "60";
            case Width::W70:
                return "70";
            case Width::W80:
                return "80";
            case Width::W90:
                return "90";
        }
    }
}

class Door {

    public $category;
    public $type;
    public $material;
    public $width;
    public $count;
    public $price;
    public $info;
    public $frame;
    public $assembly;

    /* function __construct($c, $t, $m, $w, $cn) {
        $this->category = $c;
        $this->type = $t;
        $this->material = $m;
        $this->width = $w;
        $this->count = $cn;
        $this->price = $this->getDoorPrice($t);
        $this->frame = true;
    }

    function __construct($c, $t, $m, $w, $cn, $nfo) {
        $this->category = $c;
        $this->type = $t;
        $this->material = $m;
        $this->width = $w;
        $this->count = $cn;
        $this->info = $nfo;
        $this->price = $this->getDoorPrice($t);
        $this->frame = true;
    } */

    function __construct($c, $t, $m, $w, $cn, $nfo, $frame, $assembly) {
        $this->category = $c;
        $this->type = $t;
        $this->material = $m;
        $this->width = $w;
        $this->count = $cn;
        $this->info = $nfo;
        $this->price = $this->getDoorPrice($t);
        $this->frame = $frame;
        $this->assembly = $assembly;
    }

    function isWidthSelectedText($mWidth) {
        if($this->width == $mWidth){
            return "selected";
        }
        return "";
    }

    function getFullPrice() {
        global $cena_zarubne, $cena_zarubne_akcia, $cena_montaze;
        $frame = 0;
        $assembly = 0;
        if($this->frame){
            $frame = $cena_zarubne;
            if (strcasecmp($this->type, "v1") == 0) {
                $frame = $cena_zarubne_akcia;
            }
        }
        if($this->assembly){
            $assembly = $cena_montaze;
        }
        return $this->count * ($this->price + $frame + $assembly);
    }

    function getWidthString() {
        return Width::getWidthString($this->width);
    }

    function getDoorPrice($mKey){
        $price = 0;
        global $doors_path;

        try {
            $prices = getArrayFromJsonFile($doors_path. "/" . $this->category . "/price.json");
            
            if(array_key_exists($mKey, $prices)){
                $price = $prices[$mKey];
            }
        }
        catch(Exception $e){

        }

        return $price;
    }

    function changeValueOf($fc, $value) {
        switch($fc){
            case "changeWidth":
                $this->width = Width::getWidthFromString($value);
                break;
            case "changeCount":
                $this->count = $value;
                break;
            case "changeInfo":
                $this->info = $value;
                break;
            case "changeFrame":
                $this->frame = (bool) $value;
                break;
            case "changeAssemble":
                $this->assembly = (bool) $value;
                break;
        }
    }

    function getHTMLdoor($idx) {
        global $material_path, $doors_path, $povrchova_uprava, $typ_dveri, $currency;

        ob_start();
        include 'cart-item.php';
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    //volane len z CP
    function getHTMLdoorRes($idx) {
        $html;
        try{
            $html = $this->getHTMLdoor($idx);
        }catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba zobrazenia. Prosím refreshujte stránku použitím tlačidla F5.",
                'result'=> $e->getMessage()
            );
        }
        
        return array(
            'sucess' => true,
            'message' => "OK",
            'result'=> $html
        );
    }

}

class CP {
    public $doors;      // Door::class
    public $assembly;   // obsolete - nepouziva sa - pre kazde dvere zvlast
    public $seal;       // bool
    public $putty;      // bool
    public $ironFrame;  // bool
    public $floor3;     // bool
    public $thickerFrame; // integer
    public $higherFrame; // integer
    public $distance; // integer
    public $doorLiners; // integer oblozka

    function __construct() {
        //$this->$doors = [];                                                                                                            
    }

    //gettre

    function getDistance() {
        $dist = 0;
        if ($this->distance != null && $this->distance > 0) {
            $dist = $this->distance;
        }
        return $dist;
    }

    function getDoorLiners() {
        $dist = 0;
        if ($this->doorLiners != null && $this->doorLiners > 0) {
            $dist = $this->doorLiners;
        }
        return $dist;
    }

    //funkcie

    function getDoorNumber() {
        $count = 0;
        if(!empty($this->doors)){
            foreach($this->doors as $door) {
                $count += $door->count;
            }
        }
        return $count;
    }

    function getFullPriceNoAdd() {
        $price = 0;
        if(!empty($this->doors)){
            foreach($this->doors as $door) {
                $price += $door->getFullPrice();
            }
        }
        return $price;
    }

    function getAssemblyPrice() {
        global $cena_montaze;
        $price = 0;
        if ($this->assembly) {
            $price += $this->getDoorNumber() * $cena_montaze;
        }
        //return $price; zrusene
        return 0;
    }

    function getSealPrice() {
        global $cena_tesnenia;
        $price = 0;
        if ($this->seal) {
            $price += $this->getDoorNumber() * $cena_tesnenia;
        }
        return $price;
    }

    function getPuttyPrice() {
        global $cena_tmelenia;
        $price = 0;
        if ($this->putty) {
            $price += $this->getDoorNumber() * $cena_tmelenia;
        }
        return $price;
    }

    function getIronPrice() {
        global $cena_obkladu_zarubne;
        $price = 0;
        if ($this->ironFrame) {
            $price += $this->getDoorNumber() * $cena_obkladu_zarubne;
        }
        return $price;
    }

    function getFloor3Price() {
        global $cena_vynasania;
        $price = 0;
        if ($this->floor3) {
            $price += $this->getDoorNumber() * $cena_vynasania;
        }
        return $price;
    }

    function getThickerFramePrice() {
        global $cena_priplatok_hrubsia_zaruben;
        $price = 0;
        if ($this->thickerFrame != null && $this->thickerFrame > 0) {
            $price += $this->thickerFrame * $cena_priplatok_hrubsia_zaruben;
        }
        return $price;
    }

    function getHigherFramePrice() {
        global $cena_priplatok_vyssia_zaruben;
        $price = 0;
        if ($this->higherFrame != null && $this->higherFrame > 0) {
            $price += $this->higherFrame * $cena_priplatok_vyssia_zaruben;
        }
        return $price;
    }

    function getDistancePrice() {
        global $cena_km;
        $price = 0;
        if ($this->distance != null && $this->distance > 0) {
            $price += $this->distance * $cena_km;
        }
        return $price;
    }

    function getLinerPrice() { // oblozky
        global $cena_zarubne;
        $price = 0;
        if ($this->doorLiners != null && $this->doorLiners > 0) {
            $price += $this->doorLiners * $cena_zarubne;
        }
        return $price;
    }    

    function getFullPrice() {
        global $cena_montaze, $cena_tesnenia;

        $price = 0;
        if(!empty($this->doors)){
            foreach($this->doors as $door) {
                $price += $door->getFullPrice();
            }
            $price += $this->getAssemblyPrice();
            $price += $this->getSealPrice();
            $price += $this->getPuttyPrice();
            $price += $this->getIronPrice();
            $price += $this->getFloor3Price();
            $price += $this->getThickerFramePrice();
            $price += $this->getHigherFramePrice();
            $price += $this->getDistancePrice();
            $price += $this->getLinerPrice();
        }
        return $price;
    }

    function getPriceOf($id) {
        if (array_key_exists($id, $this->doors)){
            $door = $this->doors[$id];
            return $door->getFullPrice();
        } else {
            throw new Exception("Bad ID.");
        }
    }

    function addDoor($door) {
        $idx = 1;
        try {
            if(empty($this->doors)){
                $this->doors = [ $idx => $door ];
            } else {
                $idx = max(array_keys($this->doors))+1;
                $this->doors[$idx] = $door;
            }
        }catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri ukladaní. Dvere neboli uložené. Prosím refreshujte stránku použitím tlačidla F5.",
                'result'=> $e->getMessage()
            );
        }
        
        return end($this->doors)->getHTMLdoorRes($idx);
    }

    function removeDoorAtPosition($key) {
        try{
            unset($this->doors[$key]);
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri mazaní položky",
                'result'=> $e->getMessage()
            );
        }
        return sucessResult();
    }

    function cloneDoorAtPosition($key) {
        try {
            $door = $this->doors[$key];
            $cloneDoor = clone $door;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri kopírovaní",
                'result'=> $e->getMessage()
            );
        }
        
        return $this->addDoor($cloneDoor);
    }

    function changeValueOf($fc, $id, $value) {
        try{
            $door = $this->doors[$id];
            $door->changeValueOf($fc, $value);
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setAssemblyRes($value){
        try{
            $this->assembly = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setSealRes($value){
        try{
            $this->seal = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }
    
    function setPuttyRes($value){
        try{
            $this->putty = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setIronFrameRes($value){
        try{
            $this->ironFrame = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setFloor3Res($value){
        try{
            $this->floor3 = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setThickerFrameRes($value){
        try{
            $this->thickerFrame = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setHigherFrameRes($value){
        try{
            $this->higherFrame = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setDistanceRes($value){
        try{
            $this->distance = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function setDoorLinersRes($value){
        try{
            $this->doorLiners = $value;
        }
        catch(Exception $e){
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result'=> $e->getMessage()
            );
        }

        return sucessResult();
    }

    function doPostFunction($POST) {
        switch($POST['function']) {
            case "remove":
                if(isset($POST['position'])){
                    return $this->removeDoorAtPosition($POST['position']);
                }
            break;
            case "clone":
                if(isset($POST['position'])){
                    return $this->cloneDoorAtPosition($POST['position']);
                }
            break;
            case "changeWidth":
            case "changeCount":
            case "changeInfo":
            case "changeFrame";
            case "changeAssemble";
                if(isset($POST['position']) && isset($POST['newValue'])){
                    return $this->changeValueOf($POST['function'], $POST['position'], $POST['newValue']);
                }
            break;
            case "setAssembly":
                if(isset($POST['newValue'])){
                    return $this->setAssemblyRes($POST['newValue']);
                }
            break;
            case "setSeal":
                if(isset($POST['newValue'])){
                    return $this->setSealRes($POST['newValue']);
                }
            break;
            case "setPutty":
                if(isset($POST['newValue'])){
                    return $this->setPuttyRes($POST['newValue']);
                }
            break;
            case "setIronFrame":
                if(isset($POST['newValue'])){
                    return $this->setIronFrameRes($POST['newValue']);
                }
            break;
            case "setFloor3":
                if(isset($POST['newValue'])){
                    return $this->setFloor3Res($POST['newValue']);
                }
            break;
            case "setThickerFrame":
                if(isset($POST['newValue'])){
                    return $this->setThickerFrameRes($POST['newValue']);
                }
            break;
            case "setHigherFrame":
                if(isset($POST['newValue'])){
                    return $this->setHigherFrameRes($POST['newValue']);
                }
            break;
            case "setDistance":
                if(isset($POST['newValue'])){
                    return $this->setDistanceRes($POST['newValue']);
                }
            break;
            case "setDoorLiners":
                if(isset($POST['newValue'])){
                    return $this->setDoorLinersRes($POST['newValue']);
                }
            break;
        }
        //ak nic nevolalo
        return array(
            'sucess' => false,
            'message' => "Chyba volania",
            'result'=> "Error. Unknown function or out of index."
        );
    }
}

function sucessResult() {
    return array(
        'sucess' => true,
        'message' => "OK",
        'result'=> "OK"
    );
}

function fixObject (&$object)
{
  if (!is_object ($object) && gettype ($object) == 'object')
    return ($object = unserialize (serialize ($object)));
  return $object;
}

function getCategoryFromDoorType($typ){
    if($typ != null) {
        switch(strtoupper(substr($typ, 0, 1))) {
            case "A":
                return "Alica";
            case "K":
                return "Kristina";
            case "O":
                return "Ornela";
            case "P":
                return "Petra";
            case "S":
                return "Simona";
            case "V":
                return "Vanesa";
            case "Z":
                return "Zuzana";
            case "R":
                return "Renata";
        }
    }
}

?>