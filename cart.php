<?php
include_once "cart-model.php";
?>

<div class="hide" id="price-offer-wrapper">
    <div class="hide" id="price-offer-add-item-window">
        <h2 id="price-offer-add-title"></h2><i class="fas fa-times" id="price-offer-add-close-button"></i>
        <form name="priceOfferAdd" id="priceOfferAddForm">
            <div class="clearfix">
                <h4>Šírka dverí</h4>
                <select class="price-offer-width" id="price-offer-width-add" name="width">
                    <option value="W60" selected>60</option>
                    <option value="W70">70</option>
                    <option value="W80">80</option>
                    <option value="W90">90</option>
                </select>
                </br>
                <h4>Počet</h4>
                <input type="number" class="price-offer-count" id="price-offer-count-add" placeholder="" value="1" min="1" readonly>
                <i class="fas fa-minus fa-button"></i>
                <i class="fas fa-plus fa-button"></i>
                </br>
                <div>
                    <input type="checkbox" id="price-offer-frame-add" class="checkbox" name="frame" checked>
                    <label for="frame" class="checkbox-label">Zárubeň</label>
                </div>
                <textarea type="text" id="price-offer-info-add" placeholder="Poznámka"></textarea>
                <input type="submit" name="addPO" value="Pridať">
            </div>
        </form>
    </div>
    <div id="price-offer-window">
        <i class="fas fa-times" id="price-offer-close-button"></i>
        <h2 id="price-offer-title">Cenová ponuka</h2>
        <div id="price-offer-content">
            <div id="price-offer-items-container">
                <?php
                    $isImpty = true;
                    if (array_key_exists('priceOffer', $_SESSION) && !is_null($_SESSION['priceOffer'])) {
                        $priceOffer = fixObject($_SESSION['priceOffer']);
                        if(isset($priceOffer) && isset($priceOffer->doors)){
                            foreach($priceOffer->doors as $idx => $door){
                                $isImpty = false;

                                echo $door->getHTMLdoor($idx);

                /* <!-- <div class="price-offer-line-item clearfix">
                    <div class="door">
                        <img class="" <?php echo 'src="' . $material_path . '/' . $door->material .'.png"';?>></img>
                        <img class="" <?php echo 'src="' . $doors_path . '/' . $door->category . "/" . $door->type .'.png"';?>></img>
                        <img class="rel" src="./images/zarubna.png"></img>
                    </div>
                    <div class="price-offer-line-content" door-id="<?php echo $idx; ?>">
                        <div class="price-offer-line-info">
                            <h4><?php echo $povrchova_uprava; ?></h4>
                            <span class="price-offer-line-info-text"><?php echo getMaterialNameByKey($door->material); ?></span>
                            </br><h4><?php echo $typ_dveri; ?></h4>
                            <span class="price-offer-line-info-text"><?php echo strtoupper($door->type); ?></span>
                        </div>
                        <h4>Šírka dverí</h4>
                        <select class="price-offer-width" name="width">
                            <option value="W60" <?php echo $door->isWidthSelectedText(Width::W60); ?>>60</option>
                            <option value="W70" <?php echo $door->isWidthSelectedText(Width::W70); ?>>70</option>
                            <option value="W80" <?php echo $door->isWidthSelectedText(Width::W80); ?>>80</option>
                            <option value="W90" <?php echo $door->isWidthSelectedText(Width::W90); ?>>90</option>
                        </select>
                        </br>
                        <h4>Počet</h4>
                        <input type="number" class="price-offer-count" placeholder="" value="<?php echo $door->count; ?>" min="1" readonly>
                        <i class="fas fa-minus fa-button"></i>
                        <i class="fas fa-plus fa-button"></i>
                        </br>
                        <input type="text" class="price-offer-info" placeholder="Poznámka" value="<?php echo $door->info; ?>">
                        </br>
                        <i class="far fa-clone fa-button"></i>
                        <i class="fas fa-trash price-offer-remove-item"></i>
                    </div>
                </div> --> */
                
                            }
                        }
                    } else {
                        $_SESSION['priceOffer'] = new PriceOffer();
                    }
                ?>
            </div>
            <?php
                $emptyTextClass = "hide";
                if($isImpty){
                    $emptyTextClass = "";
                }
                echo '<div id="price-offer-empty-text" class="'.$emptyTextClass.'">Cenová ponuka je prázdna. </br></div>';
            ?>
            <div class="<?php if($isImpty) echo "hide";?>" id="price-offer-additional">
                *Cena dverí sa môže líšit podľa šírky dverí. Ceny sú uvedené bez DPH.</br>
                <h3>Dodatočné služby</h3>
                <!-- <div>
                    <input type="checkbox" id="price-offer-assembly" class="checkbox" name="assembly" <?php if($priceOffer->assembly) echo "checked"; ?>>
                    <label for="assembly" class="checkbox-label">Montáž <?php echo $cena_montaze.$currency; ?>/dvere</label>
                </div> -->
                <div>
                    <input type="checkbox" id="price-offer-putty" class="checkbox" name="putty" <?php if($priceOffer->putty) echo "checked"; ?>>
                    <label for="putty" class="checkbox-label">Vytmeleni nerovností medzi stenou a zárubňou <?php echo $cena_tmelenia.$currency; ?>/dvere</label>
                </div>
                <div>
                    <input type="checkbox" id="price-offer-seal" class="checkbox" name="seal" <?php if($priceOffer->seal) echo "checked"; ?>>
                    <label for="seal" class="checkbox-label">Silikónové tesnenie do zárubne <?php echo $cena_tesnenia.$currency; ?>/dvere</label>
                </div>
                <div>
                    <input type="checkbox" id="price-offer-ironFrame" class="checkbox" name="ironFrame" <?php if($priceOffer->ironFrame) echo "checked"; ?>>
                    <label for="ironFrame" class="checkbox-label">Obklad oceľových zárubní <?php echo $cena_obkladu_zarubne.$currency; ?>/dvere</label>
                </div>
                <div>
                    <input type="checkbox" id="price-offer-floor3" class="checkbox" name="floor3" <?php if($priceOffer->floor3) echo "checked"; ?>>
                    <label for="floor3" class="checkbox-label">Vynášanie na 3. a vyššie poschodie (bez víťahu)</label>
                </div>
                <div>
                    <input type="number" id="price-offer-thickerFrame" class="price-offer-count noBellowZero" name="thickerFrame" placeholder="" value="<?php if($priceOffer->thickerFrame > 0) echo $priceOffer->thickerFrame; ?>" min="0">
                    <label for="thickerFrame" class="checkbox-label">Príplatok za obložky hrubšie ako 20cm <?php echo $cena_priplatok_hrubsia_zaruben.$currency; ?>/extra 10cm</label>
                </div>
                <div>
                    <input type="number" id="price-offer-higherFrame" class="price-offer-count noBellowZero" name="higherFrame" placeholder="" value="<?php if($priceOffer->higherFrame > 0) echo $priceOffer->higherFrame; ?>" min="0">
                    <label for="higherFrame" class="checkbox-label">Príplatok dvere vyššie ako 207cm <?php echo $cena_priplatok_vyssia_zaruben.$currency; ?>/dvere</label>
                </div>
                <div>
                    <input type="number" id="price-offer-distance" class="price-offer-count noBellowZero" name="distance" placeholder="" value="<?php if($priceOffer->distance > 0) echo $priceOffer->distance; ?>" min="0">
                    <label for="distance" class="checkbox-label">Dovoz sa počíta počet km od výroby v obci Čachtice</label>
                </div>
                <div>
                    <input type="number" id="price-offer-doorLiners" class="price-offer-count noBellowZero" name="doorLiners" placeholder="" value="<?php if($priceOffer->doorLiners > 0) echo $priceOffer->doorLiners; ?>" min="0">
                    <label for="doorLiners" class="checkbox-label">Počet obložiek bez dverí</label>
                </div>
                
                <div class="rtl price-offer-full-price">
                    <span id="price-offer-full-price-number"><?php echo $priceOffer->getFullPrice(); ?></span><?php echo $currency; ?>
                </div>
                    Cena je len orientačná. Presná cenová ponuka bude vypracovaná až po potvrdení objednávky a zameraní dverí.
            </div>
            <h3>Kontaktné informácie</h3>
            <span class="price-offer-message hide" id="price-offer-errorMessage1">Povinné polia nie sú vyplnené!</span>
            <span class="price-offer-message hide" id="price-offer-errorMessage2">E-mail má nesprávny formát.</span>
            <span class="price-offer-message hide" id="price-offer-errorMessage3">E-mail nebol odoslaný!</span>
            <span class="price-offer-message success-message hide" id="price-offer-successMessage1">E-mail bol odoslaný.</span>
            <span class="price-offer-message hide" id="reCaptcha-errorMessage1">Prosím zaškrtnite políčko "Nie som robot"</span>
            <span class="price-offer-message hide" id="reCaptcha-errorMessage2">Neprešli ste testom na robota.</span>

            <form name="priceOfferMail" id="priceOfferMailForm">
                <div class="price-offer-line">
                    <i class="far fa-user"></i>
                    <input type="text" class="" id="price-offer-name" onfocusout="validateOfferValue('name')" placeholder="Vaše meno**" <?php if( isset($_SESSION['name']) ) echo 'value="'.$_SESSION['name'].'"';?>>
                </div>
                <div class="price-offer-line">
                    <i class="far fa-envelope"></i>
                    <input type="text" class="" id="price-offer-mail" onfocusout="validateOfferValue('mail')" placeholder="Váš email**" <?php if( isset($_SESSION['mail']) ) echo 'value="'.$_SESSION['mail'].'"';?>>
                </div>
                <div class="price-offer-line">
                    <i class="fas fa-mobile-alt"></i>
                    <input type="number" class="" id="price-offer-mobile" placeholder="Váš telefón" <?php if( isset($_SESSION['phone']) ) echo 'value="'.$_SESSION['phone'].'"';?>>
                </div>
                <div class="price-offer-line">
                    <i class="far fa-file-alt"></i>
                    <input type="text" class="" id="price-offer-note" placeholder="Poznámka">
                </div>

<!--                <script src="https://www.google.com/recaptcha/api.js?hl=sk"></script>-->
                <!--<div class="g-recaptcha" data-sitekey="6LdTCasUAAAAAHdKMyLCMeIyp6n5y8rQM5OXwxEW">
                    <div style="width: 304px; height: 78px;">
                        <div>
                            <iframe title="reCAPTCHA" src="https://www.google.com/recaptcha/api2/anchor?ar=1&amp;k=6LdTCasUAAAAAHdKMyLCMeIyp6n5y8rQM5OXwxEW&amp;co=aHR0cDovL3d3dy52aXp1YWxpemFjaWEtZHZlcmkuc2s6ODA.&amp;hl=sk&amp;v=_7Co1fh8iT2hcjvquYJ_3zSP&amp;size=normal&amp;cb=svbg4n28rz4n" width="304" height="78" role="presentation" name="a-bqsr1vf3zdqy" frameborder="0" scrolling="no" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox"></iframe>
                        </div>
                        <textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid rgb(193, 193, 193); margin: 10px 25px; padding: 0px; resize: none; display: none;"></textarea>
                    </div>
                    <iframe style="display: none;"></iframe>
                    
                    site 6Ld5jQMrAAAAAEgMhJDxVkJ7Du5Zpw62pBzUL4wl
                    server 6Ld5jQMrAAAAAFTSKLPKeyT3nrmc6jga1bbN8lqd
                    
                </div>
                
                    <div class="g-recaptcha" data-sitekey="6LdTCasUAAAAAHdKMyLCMeIyp6n5y8rQM5OXwxEW"></div>
                    <br/>-->
                    <input type="submit" name="submit" value="Odoslať">
                    <a href="downloadPDF.php" target="_blank"><span class="button"><i class="fas fa-file-download"></i></span></a>
            </form>
            <!--
            <form name="priceOfferPDF" id="priceOfferPDFform">
                <input type="submit" name="submit" value="Generovať PDF">
            </form>-->
            <span id="price-offer-req-text">**Povinné pole</span>
        </div>
    </div>
</div>