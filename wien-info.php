    <div class="material_container container clearfix" id="wien_types">
        <h2>možnosti prevedenia konštrukcie</h2>
        <span>Pre dvere z kategória Wien sú typické ozdobné typy líšt, kaziet a obložiek. Na výber je z nasledovných modelov:</span>
        <?php       
            
            $wien = getArrayFromJsonFile("./images/wien/conf.json");

            $types = $wien['typ'];
            $ineList = $wien['ine'];

            $i=1;
            foreach(array_keys($types) as $typ){
        ?>
            <div class="item">
                <div class="material">
                    <img class="material_img" <?php echo 'src="./images/wien/' . $typ . '.jpg"'; ?>>
                </div>
                <span class="material_name"><?php echo $types[$typ]; ?></span>
            </div>
        <?php
                for($j=7; $j>1 ; $j--){
                    if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                }
                $i++;
            }
        ?>
            <div class="clearfix"></div>
            <span>Standardom k dverám kategórie Wien sú zlaté kľučky ALT WIEN (piškótový tvar, ktorý sa pre tento dizajn dverí používa už stovky rokov). Tiež sú v štandarde zlaté zámky a zlaté pánty.</span>
            <div class="clearfix"></div>
        <?php
            $i=1;
            foreach(array_keys($ineList) as $ine){
        ?>
                <div class="item">
                    <div class="material">
                        <img class="material_img" <?php echo 'src="./images/wien/' . $ine . '.jpg"'; ?>>
                    </div>
                    <span class="material_name"><?php echo $ineList[$ine]; ?></span>
                </div>
            <?php
                for($j=7; $j>1 ; $j--){
                    if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                }
                $i++;
            }
        ?>
    </div>