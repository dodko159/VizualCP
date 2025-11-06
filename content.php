<?php
    $d=5; //dvere
    $f=1; //podlaha
    $h=1; //klucka
    $defR = 4; //miestnost

    //nacitanie kategorii z priecinku
    $categories = loadCategories();

    // nacitanie kategorie z url
    $category = loadCategoryFromURL($categories);

    //nacitanie miestnosti
    $rooms = getArrayFromJsonFile($room_path . "/conf.json");

    //nacitanie akcii
    $akcie = getArrayFromJsonFile("./akcie.json");

    // nacitanie miestnosti z url
    $r = loadRoomFromURL($rooms, $defR);

    $doorMinDir = $doors_path. "/" . removeAccents($category['name']) . "/min";

    //def material
    $m = $category['material'];

    //nacitanie obrazkov danej kategorie
    //$doors = loadImagesFromDirectory($doorMinDir);
    $prices = getArrayFromJsonFile($doors_path. "/" . removeAccents($category['name']) . "/price.json");
    //natsort($doors);
    $doors = array_keys($prices);

    //def doors
    $d = returnPositionOfDoorType($category['type'], $doors, $d-1) + 1;

    //nacitanie materialov
    $materials = getArrayFromJsonFile($material_path . "/conf.json");
    $init_material = array_keys($materials[array_keys($materials)[0]])[$m-1];

    //nacitanie podlah
    $floors = getArrayFromJsonFile($floor_path . "/conf.json");
    $init_floor = array_keys($floors)[$f-1];

    //miestnost po nacitani
    $init_room = array_keys($rooms)[$r-1];

    //nacitanie miestnosti
    $handles = getArrayFromJsonFile($handle_path . "/conf.json");
    $init_handle = array_keys($handles)[$h-1];

    //nacitanie skiel
    $glasses = getArrayFromJsonFile($glass_path . "/conf.json");

    //ci je specialny typ WIEN
    $isWien = $category['wien'] == 1;
    if ($isWien) {
        echo "<script>var isWien = true;</script>";
    } else {
        echo "<script>var isWien = false;</script>";
    }
?>

<div class="content_container" id="content_back">
    <!-- <div id="full_screen_btn"></div> -->
    <canvas id="canvas" width="1920" height="999"></canvas>

    <div class="content">
        <div class="left_side">
            <div id="loading" class="hide">
                <div class="loading_img">
                    <img src="./assets/img/loading.gif" alt="<?php echo $loading; ?>">
                </div>
            </div>
            <div class="head_of_category">
                <h1 class="textShadow"><span class="hide-1200"><?php echo $head_category_titele_prefix;?></span><span class="show-1200 hide-780"><?php echo $head_category_titele_prefix_short;?></span>
                <span class="category_title_name"><?php echo $category['name'];?></span></h1>
                <div class="category_desc textShadow"><?php loadCategoryDesc($doors_path. "/" . removeAccents($category['name']));?></div>
            </div>

            <!-- HELPER //////////// -->

            <div class="selected_door_container">
                <div class="selected_door_comb" id="selected_door_combination">
                    <div class="selected">
                        <img id="selected_door" src="<?php 
                            echo $doors_path . "/" . removeAccents($category['name']) . "/" . $doors[$d-1] . ".png";
                        ?>">
                    </div>
                    <div class="selected">
                        <img id="selected_material" src="<?php 
                            echo $material_path . "/" . $init_material . ".png";
                        ?>">
                    </div>

                    <img id="selected_room" src="<?php
                        echo $room_path . "/" . $init_room . ".png";
                    ?>">

                    <img id="selected_floor" src="<?php
                        echo $floor_path . "/" . $init_floor . ".png";
                    ?>">

                    <img id="selected_handle" src="<?php
                        echo $handle_path . "/" . $init_handle . ".png";
                    ?>">
                </div>
            </div>

            <!-- //////////// -->
        </div>

        <div class="under_door textShadow" id="under_door">
            <span class="price_container"><?php echo $priceText; ?>
            <?php
            $values = array_keys($prices);
            $i = 1;
            foreach($prices as $price){
                if($i==$d): 
                    echo '<span class="sel" id="price_'.$values[$i-1].'">'.$price.'</span>';
                else: 
                    echo '<span class="desel" id="price_'.$values[$i-1].'">'.$price.'</span>';
                endif;
                $i++;
            }
        
            ?>
            <?php echo $currency; ?> </span>    <br>
            <span id="under_door_desc"><?php echo $imageDesc; ?></span>
			<span id="under_door_desc"><?php if ($isWien) {echo $framePriceDescWien;} else {echo $framePriceDesc;} ?></span>
            <?php if ($enablePO) { ?>
            <div id="under_door_buttons">
                <span class="minimize" id="po_link_add">
                    <?php echo $addToCP; ?>
                </span>
                <span class="minimize" id="po_link">
                    <i class="fas fa-shopping-cart"></i>
                </span>
            </div>
            <?php } ?>
        </div>

        <?php //sekcia vyberu pozadia a kategorii?>

        <div class="room_selection_container selection_container rtl">
            <div id="room_section_openclose_btn" class="section_openclose_btn">
                <div class="openclose_btn_text_area">
                    <div class="openclose_btn_img arrow-left"></div>
                    <span class="openclose_btn_text"><?php echo $zmenit_miestnos; ?></span>
                </div>
            </div>
            <div class="selection_content ltr">
                <div class="doors_container categories container clearfix">
                    <h2><?php echo $categories_title; ?></h2>
                    <?php
                        $i=1;
                        foreach($categories as $cat){

                            $name = $cat['name'];
                            $cat['name'] = removeAccents($cat['name']);

                            $doorDir = $doors_path. "/" . $cat['name'] . "/min";
                    ?>
                            <div class="door">
                                <a href="./?category=<?php echo $cat['name']; ?>" <?php echo 'id="category_' . $cat['name'] . '"';?> class="<?php if(removeAccents($category['name'])==$cat['name']): echo 'sel'; else: echo 'desel'; endif; ?>">      
                                    <div class="door_img">
                                        <img class="door_closed" <?php echo 'src="' . $doorDir . '/' . $cat['type'] .'.png"';?>></img>
                                        <img class="door_openned" <?php echo 'src="' . $doorDir . '/open/' . $openDoorPrefix . $cat['type'] . '.png"'; ?>>
                                    </div> 
                                </a>  
                                <span class="door_name"><?php echo $name; ?></span>                
                            </div>
                    <?php
                            for($j=7; $j>1 ; $j--){
                                if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                            }
                            $i++;
                        }
                    ?>
                </div>

                <?php //sekcia vyberu pozadia - miestnosti ?>

                <div class="rooms_container container clearfix">
                <h2><?php echo $rooms_title; ?></h2>
                    <?php
                        $i=1;

                        foreach($rooms as $image){
                            $src = array_search($image, $rooms);
                    ?>
                            <div class="room">
                                <a href="javascript:void(0);" <?php echo 'id="room_'. $i .'_' . $src . '"';?> class="<?php if($i==$r): echo 'sel'; else: echo 'desel'; endif; $i++; ?>">      
                                    <div class="room_img">
                                        <img class="" <?php echo 'src="' . $room_path . '/min/' . $src .'.jpg"';?>></img>
                                    </div> 
                                </a>  
                                <span class="room_name"><?php echo $image; ?></span>                
                            </div>
                    <?php
                        }
                    ?>
                </div>
                <?php //sekcia vyberu pozadia - podlahy ?>

                <div class="material_container container clearfix" id="floor_materials">
                <h2><?php echo $floors_title; ?></h2>

                <div class="item">
                        <a href="javascript:void(0);" class="<?php if(1==$f): echo 'sel'; else: echo 'desel'; endif; ?>" <?php echo 'id="floor_default"';?>>
                            <div class="material">
                                <img class="material_img" <?php echo 'src="' . $floor_path . '/min/default.jpg"'; ?>>
                            </div>
                        </a>
                        <span class="material_name"><?php echo $floor_def_name; ?></span>
                    </div>

                <?php  
                        
                    $i=2;

                    foreach($floors as $floor){
                        $src = array_search($floor, $floors);
                ?>
                    <div class="item">
                        <a href="javascript:void(0);" class="<?php if($i==$f): echo 'sel'; else: echo 'desel'; endif;?>" <?php echo 'id="floor_' . $src . '"';?>>
                            <div class="material">
                                <img class="material_img" <?php echo 'src="' . $floor_path . '/min/' . $src . '.jpg"'; ?>>
                            </div>
                        </a>
                        <span class="material_name"><?php echo $floor; ?></span>
                    </div>
                <?php
                        for($j=7; $j>1 ; $j--){
                            if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                        }
                        $i++;
                    }
                ?>
                </div>
            </div>
                <div class="footer ltr">
                    <span class="left">
                        <span>Všetky uvedené ceny sú bez DPH.</span></br>
                        <span class="footer_name">Vytvorili: Peter </span>Sučanský ml. & <span class="footer_name">Dominik </span>Janíček,
                        <br>
                        Used: <a href="https://github.com/rikschennink/fitty" target="_blank">Fitty</a>, 
                        <a href="http://labs.rampinteractive.co.uk/touchSwipe/demos/index.html" target="_blank">touchSwipe</a>, 
                        <a href="http://fancybox.net/" target="_blank">Fancybox</a>
                        <br>
                        <span id="copyright_text">Copyright&nbsp;</span>©&nbsp;2023
                    </span>
                </div>
        </div>

        <?php //sekcia vyberu dveri ?>

        <div class="door_selection_container selection_container">
            <div id="door_section_openclose_btn" class="section_openclose_btn">
                <div class="openclose_btn_text_area">
                    <div class="openclose_btn_img arrow-right"></div>
                    <span class="openclose_btn_text"><?php echo $zmenit_dvere; ?></span>
                </div>
            </div>
            <!-- dvere -->
            <div class="selection_content">
                <div class="doors_container container clearfix" id="doors">
                <h2><?php echo $door_types . $category['name']; ?></h2>
                    <?php
                        $i=1;

                        foreach($doors as $image){
                    ?>
                            <div class="door">
                                <a href="javascript:void(0);" <?php echo 'id="door_' . $image . '"';?> class="<?php if($i==$d): echo 'sel'; else: echo 'desel'; endif; ?>">      
                                    <div class="door_img">
                                        <img class="door_closed" <?php echo 'src="' . $doorMinDir . '/' . $image .'.png"';?>></img>
                                        <img class="door_openned" <?php echo 'src="' . $doorMinDir . '/open/' . $openDoorPrefix . $image . '.png"'; ?>>
                                    </div> 
                                </a>  
                                <span class="door_name"><?php echo str_replace("_", " ", $image); ?></span>                
                            </div>
                    <?php
                            for($j=7; $j>1 ; $j--){
                                if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                            }
                            $i++;
                        }
                        
                    ?>
                </div>
                    <?php 
                        if($isWien) {
                           // echo '<span class="clearfix">S- SKLO</span>';
                        }
                    ?>
                <!-- material -->
                <div class="material_container container clearfix" id="door_materials">
                <?php       
                    $k=1;
                    foreach(array_keys($materials) as $material_type){
                        if($k!=1 && $isWien) {
                            continue;
                        }
                ?>
                        <div class="clearfix"></div>
                        <h2><?php 
                        if($isWien) {
                            echo $povrchova_uprava;
                        } else {
                            echo $material_type;
                        }
                        ?></h2>
                <?php
                        $i=1;
                        foreach($materials[$material_type] as $material){
                            $src = array_search($material, $materials[$material_type]);
                            if($isWien) {
                                $material = "Biele striekané";
                                if($i!=$m) {
                                    continue;
                                }
                            }
                ?>
                    <div class="item">
                        <a href="javascript:void(0);" class="<?php if($i==$m && $k==1): echo 'sel'; else: echo 'desel'; endif; ?>" <?php echo 'id="material_' . $src . '"';?>>
                            <div class="material">
                                <img class="material_img" <?php echo 'src="' . $material_path . '/min/' . $src . '.jpg"'; ?>>
                            </div>
                        </a>
                        <span class="material_name"><?php echo $material; ?></span>
                    </div>
                <?php
                            for($j=7; $j>1 ; $j--){
                                if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                            }
                            $i++;
                        }
                        $k++; //???
                    }
                ?>
                </div>

                <?php
                    if ($isWien) {

                        include "wien-info.php";

                    } else { //else wien
                ?>
                <!-- klucky -->
                <div class="material_container container clearfix" id="handle_types">
                <h2><?php echo $handles_title; ?></h2>
                <?php       
                    $i=1;

                    foreach($handles as $handle){
                        $src = array_search($handle, $handles);
                ?>
                    <div class="item">
                        <a href="javascript:void(0);" class="<?php if($i==$h): echo 'sel'; else: echo 'desel'; endif; ?>" <?php echo 'id="handle_' . $src . '"';?>>
                            <div class="material">
                                <span class="material_info"><?php echo $handle["price"] . $currency; ?></span>
                                <img class="material_img" <?php echo 'src="' . $handle_path . '/min/' . $src . '.jpg"'; ?>>
                            </div>
                        </a>
                        <span class="material_name"><?php echo $handle["name"]; ?></span>
                    </div>
                <?php
                        for($j=7; $j>1 ; $j--){
                            if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                        }
                        $i++;
                    }
                ?>
                </div>

                <?php
                    } // koniec else wien
                ?>

                <!-- sklo -->
                <div class="material_container container clearfix" id="glass_types">
                    <h2><?php echo $glass_title; ?></h2>
                    <?php       
                    $i=1;
                        foreach($glasses as $glass){
                            $src = array_search($glass, $glasses);
                    ?>
                        <div class="item">
                            <a class="material" <?php echo 'href="' . $glass_path . '/' . $src . '.jpg"'; ?> data-fancybox="gallery" title="<?php echo $glass; ?>">
                                <img <?php echo 'src="' . $glass_path . '/min/' . $src . '.jpg"'; ?> class="material_img">                        
                            </a>
                            <span class="material_name"><?php echo $glass; ?></span>
                        </div>
                    <?php
                            for($j=7; $j>1 ; $j--){
                                if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                            }
                            $i++;
                        }
                    ?>
                </div>

                <!-- kategorie -->
                <div class="doors_container categories container clearfix" id="right_categories">
                    <h2><?php echo $categories_title; ?></h2>
                    <?php
                        $i=1;
                        foreach($categories as $cat){

                            $name = $cat['name'];
                            $cat['name'] = removeAccents($cat['name']);

                            $doorDir = $doors_path. "/" . $cat['name'] . "/min";
                    ?>
                            <div class="door">
                                <a href="./?category=<?php echo $cat['name']; ?>" <?php echo 'id="category_' . $cat['name'] . '"';?> class="<?php if(removeAccents($category['name'])==$cat['name']): echo 'sel'; else: echo 'desel'; endif; ?>">      
                                    <div class="door_img">
                                        <img class="door_closed" <?php echo 'src="' . $doorDir . '/' . $cat['type'] .'.png"';?>></img>
                                        <img class="door_openned" <?php echo 'src="' . $doorDir . '/open/' . $openDoorPrefix . $cat['type'] . '.png"'; ?>>
                                    </div> 
                                </a>  
                                <span class="door_name"><?php echo $name; ?></span>                
                            </div>
                    <?php
                            for($j=7; $j>1 ; $j--){
                                if($i%$j==0){echo '<div class="show'.$j.'only clearfix"></div>';}
                            }
                            $i++;
                        }
                    ?>
                </div>

                <!-- akcie -->
                <div class="akcie container clearfix">
                    <h2><?php echo $akcia_title; ?></h2>
                    <?php 
                    foreach($akcie as $akcia){
                    ?>
                    <div class="akcia">
                        <div class="akcia_img">
                            <img src="<?php echo $akcia['image']; ?>">
                        </div>
                        <h2 class="akcia_title"><?php echo $akcia['title']; ?></h2>
                        <span><?php echo $akcia['text']; ?></span>
                    </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>