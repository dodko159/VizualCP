            <div class="price-offer-line-item clearfix" id="po-item-<?php echo $idx; ?>">
                <div class="door">
                    <img class="" <?php echo 'src="' . $material_path . '/' . $this->material .'.png"';?>></img>
                    <img class="" <?php echo 'src="' . $doors_path . '/' . $this->category . "/" . $this->type .'.png"';?>></img>
                    <img class="rel" src="./images/zarubna.png"></img>
                </div>
                <div class="price-offer-line-content" door-id="<?php echo $idx; ?>">
                    <div class="price-offer-line-info">
                        <h4><?php echo $povrchova_uprava; ?></h4>
                        <span class="price-offer-line-info-text"><?php echo getMaterialNameByKey($this->material); ?></span>
                        </br><h4><?php echo $typ_dveri; ?></h4>
                        <span class="price-offer-line-info-text"><?php echo strtoupper($this->type); ?></span>
                    </div>
                    <h4>Šírka dverí</h4>
                    <select class="price-offer-width" name="width">
                        <option value="W60" <?php echo $this->isWidthSelectedText(Width::W60); ?>>60</option>
                        <option value="W70" <?php echo $this->isWidthSelectedText(Width::W70); ?>>70</option>
                        <option value="W80" <?php echo $this->isWidthSelectedText(Width::W80); ?>>80</option>
                        <option value="W90" <?php echo $this->isWidthSelectedText(Width::W90); ?>>90</option>
                    </select>
                    </br>
                    <h4>Počet</h4>
                    <input type="number" class="price-offer-count" placeholder="" value="<?php echo $this->count; ?>" min="1" readonly>
                    <i class="fas fa-minus fa-button"></i>
                    <i class="fas fa-plus fa-button"></i>
                    </br>
                    <div>
                        <input type="checkbox" class="price-offer-frame checkbox" name="frame" <?php if($this->frame) echo "checked"; ?>>
                        <label for="frame" class="checkbox-label">Zárubeň</label>
                        <input type="checkbox" class="price-offer-assemble checkbox" name="assemble" <?php if($this->assembly) echo "checked"; ?>>
                        <label for="assemble" class="checkbox-label">Montáž</label>
                    </div>
                    <input type="text" class="price-offer-info" placeholder="Poznámka" value="<?php echo $this->info; ?>">
                    </br>
                    <span>
                        <i class="far fa-clone fa-button"><span class="fa-text">Duplikovať<span></i>
                        <i class="fas fa-trash price-offer-remove-item"></i>
                    </span>
                    <span class="price-offer-item-price">
                        <span class="price-offer-item-price-number"><?php echo $this->getFullPrice(); ?></span><?php echo $currency; ?>*
                    </span>
                </div>
            </div>