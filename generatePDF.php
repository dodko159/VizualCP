<?php
include_once "cart-class.php";
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function generatCPPDF() {
    global $currency;

    if (array_key_exists('priceOffer', $_SESSION)) {
        $options = new Options;
        $options->setChroot(__DIR__);
        $options->set('defaultFont', 'DejaVu Sans');
    
        $pdf = new Dompdf($options);
    
        $html = '';
        $PO = fixObject($_SESSION['priceOffer']);
        if(isset($PO) && isset($PO->doors)){
            $canSend = true;
            $doorInfo = "<table style=\"border-collapse: collapse;\"><tr>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\"></th>  
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Šírka</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Typ</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Povrchová úprava</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Počet</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Zárubňa</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Montáž</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Cena za kus*</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Cena celkom**</th>
            <th style=\"border: 1px solid #dddddd; padding: 5px;\">Poznámka</th>
            </tr>";
            $idx = 1;
            foreach($PO->doors as $door){
                $background = "";
                if($idx % 2 == 1){
                $background = ' style="background-color: #dddddd;"';
                }
                $zaruben = "Nie";
                if($door->frame){
                $zaruben = "Áno";
                }
                $montaz = "Nie";
                if($door->assembly){
                $montaz = "Áno";
                }
                $doorInfo .= "<tr$background><td style=\"border: 1px solid #dddddd; padding: 5px;\">$idx</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".$door->getWidthString()."</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".strtoupper($door->type)."</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".getMaterialNameByKey($door->material)."</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$door->count</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$zaruben</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$montaz</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".$door->price.$currency."</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">".$door->getFullPrice().$currency."</td>";
                $doorInfo .= "<td style=\"border: 1px solid #dddddd; padding: 5px;\">$door->info</td></tr>";
                $idx++;
            }

            $doorInfo .= "<tr><td colspan=\"8\" style=\"border: 0px; padding: 5px;\"><b>Spolu</b></td><td colspan=\"2\" style=\"border: 0px; padding: 5px;\"><b>".$PO->getFullPriceNoAdd().$currency."</b></td></tr>";
            $doorInfo .= "</table>";
            $doorInfo .= "*cena bez zárubne bez DPH<br>";
            $doorInfo .= "**cena celkom so zárubňov (ak je zvolená) bez DPH<br><br>";
            $doorInfo .= "<b>Dodatočné služby</b><br>";
            //$doorInfo .= "Montáž: ".$PO->getAssemblyPrice().$currency."<br>";
            $doorInfo .= "Obložky bez dverí: ".$PO->getDoorLiners()."ks - ".$PO->getLinerPrice().$currency."<br>";
            $doorInfo .= "Vytmelenie nerovností: ".$PO->getPuttyPrice().$currency."<br>";
            $doorInfo .= "Silikónové tesnenie: ".$PO->getSealPrice().$currency."<br>";
            $doorInfo .= "Obklad oceľových zárubní: ".$PO->getIronPrice().$currency."<br>";
            $doorInfo .= "Vynášanie na 3. poschodie a viac: ".$PO->getFloor3Price().$currency."<br>";

            $doorInfo .= "Príplatok za hrubšie obložky: ".$PO->getThickerFramePrice().$currency."<br>";
            $doorInfo .= "Príplatok za vysoké dvere: ".$PO->getHigherFramePrice().$currency."<br>";
            $doorInfo .= "Doprava: ".$PO->getDistance()."km - ".$PO->getDistancePrice().$currency."<br>";

            $doorInfo .= "<b>Celková cena: ".$PO->getFullPrice().$currency."</b><br>";
        }
        $html .= $doorInfo;

        $pdf->loadHtml($html);
    
        $pdf->setPaper("A4", "portrait");
        $pdf->render(); // render
       /* $name = $_POST['name'];
        if ($name != null) {
            $name = "Cenová ponuka - " . $name;
        } else {
            $name = "Cenová ponuka";
        }*/
        $pdf->addInfo("Title", "Cenová ponuka");
        $pdf->addInfo("Author", "Stolárstvo-Sučanský");
    }

    return $pdf;
}