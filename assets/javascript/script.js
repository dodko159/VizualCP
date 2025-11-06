var defFloor = "DUB-HAMILTON";
var imageWidth = 1920;
var imageHeight = 999;
var imageRatio = imageHeight/imageWidth;
var hideDuration = 800; /* po nacitani sa prepise */
var rightSectionOpen = true;
var leftSectionOpen = true;

function changeDoor(obj){
    var id = '#selected_door';
    var path = changeImgSrc(obj, id);
    $(id).attr('src', path + ".png");
    //console.log(nameFromPath(path));
    $.post("add-session-val.php", {'doorType':nameFromPath(path)});
    //sendSessionValues({'doorType':nameFromPath(path)});
}

function changeMaterial(obj){
    var id = '#selected_material';
    var path = changeImgSrc(obj, id);
    $(id).attr('src', path + ".png");
    //console.log(nameFromPath(path));
    $.post("add-session-val.php", {'material':nameFromPath(path)});
    //sendSessionValues({'material' : nameFromPath(path)});
}

function doesFileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
     
    if (xhr.status == "404") {
        return false;
    } else {
        return true;
    }
}

function nameFromPath(path){

    return path.split("/").splice(-1,1)[0];
}

function changeFloor(obj){
    var id = '#selected_floor';
    var image_url = changeImgSrc(obj, id) + ".png";

    if(doesFileExist(image_url)){
        $(id).attr('src', image_url);
    }else{
        var adr = image_url.split("/");
        adr = adr.splice(0,adr.length-1);
        $(id).attr('src', adr.join("/") + "/" + defFloor + ".png");
    }
}

function changeHandle(obj){
    var id = '#selected_handle';
    $(id).attr('src', changeImgSrc(obj, id) + ".png");
}

function changeRoom(obj){
    var id = '#selected_room';
    $(id).attr('src', changeImgSrc(obj, id) + ".png");
}

function changeImgSrc(obj, id){
    var src = $(id).attr('src');
    var adr = src.split("/");
    adr = adr.splice(0,adr.length-1);
    var nameList = $(obj).attr('id').split("_");
    nameList.shift();
    var name = nameList.join("_");
    var newAdr = adr.join("/") + "/" + name;

    return newAdr;
}

//hide left side if left is open (used when canvas is regenerate)
function hideLeftIfAllShown(){
    if(leftSectionOpen){
        setTimeout(function(){
            $('#room_section_openclose_btn').click(); 
        }, 500);
    }
}

function generateCanvas(){

    var materialSrc = $('#selected_material').attr('src');
    var doorSrc = $('#selected_door').attr('src');
    var roomSrc = $('#selected_room').attr('src');
    var doorFrameSrc = "./images/zarubna.png";
    var handleSrc = $('#selected_handle').attr('src');
    var floorSrc = $('#selected_floor').attr('src');

    var c=document.getElementById("canvas");
    var ctx=c.getContext("2d");
    var floor = new Image();
    var room = new Image();
    var material = new Image();
    var door = new Image();
    var frame = new Image();
    var handle = new Image();

    var isWienLoc = false;
    if (isWien == true) {
        isWienLoc = isWien;
    }

    $('#loading').removeClass('hide');
    hideLeftIfAllShown();

    floor.src = floorSrc;
    floor.onload = function(){
        room.src = roomSrc;
        room.onload = function(){
            material.src = materialSrc;
            material.onload = function() {
                door.src = doorSrc;
                door.onload = function() {
                    frame.src = doorFrameSrc;
                    frame.onload = function() {
                        handle.src = handleSrc;
                        handle.onload = function(){
                            ctx.drawImage(floor, 0, 0, imageWidth, imageHeight);
                            ctx.drawImage(room, 0, 0, imageWidth, imageHeight);
                            ctx.drawImage(material,140,250,290,615);
                            ctx.drawImage(door,140,250,290,615);
                            if (!isWienLoc) {
                                ctx.drawImage(handle,140,250,290,615);
                                ctx.drawImage(frame,140,250,290,615);
                            }
                            //var img = c.toDataURL("image/png");
            
                            var data = c.toDataURL();
                            var myElement = document.getElementById('content_back');
                            
                            myElement.style.backgroundImage = 'url('+data+')';
            
                            $('#loading').addClass('hide');
                        }                   
                    }
                }
            }
        }
    };
}

function updateCanvasSize(){
    $("#content_back").attr("width", document.body.clientWidth);
    $("#content_back").attr("height", document.body.clientHeight);
}

// door selection clicked - door
    $(document).ready(function(){
        $("#doors .door a").click(function(){
            if($(this).attr('class')=="desel") {
                $("#doors .door a").attr('class', 'desel');
                $(this).attr('class', 'sel');
                changeDoor(this);
                generateCanvas();
                //price
                var nameList = $(this).attr('id').split("_");
                nameList.shift();
                var name = nameList.join("_");
                $(".price_container span").attr('class', 'desel');
                $("#price_" + name).attr('class', 'sel');
            }
        });
    });

// door selection clicked - material
    $(document).ready(function(){
        $("#door_materials .item a").click(function(){
            if($(this).attr('class')=="desel") {
                $("#door_materials .item a").attr('class', 'desel');
                $(this).attr('class', 'sel');
                changeMaterial(this);
                if(checkIsDefaultFloor()){
                    changeFloor(this);
                }
                generateCanvas();
            }
        });
    });

// door selection clicked - handle
$(document).ready(function(){
    $("#handle_types .item a").click(function(){
        if($(this).attr('class')=="desel") {
            $("#handle_types .item a").attr('class', 'desel');
            $(this).attr('class', 'sel');
            changeHandle(this);
            generateCanvas();
        }
    });
});

// room selection clicked - category
$(document).ready(function(){
    $(".categories a").click(function(){
        var id = $(".room .sel").attr('id').split('_')[1];
        $(this).attr('href', this.href + "&room=" + id);
    });
});

// room selection clicked - room
$(document).ready(function(){
    $(".rooms_container .room a").click(function(){
        if($(this).attr('class')=="desel") {
            $(".rooms_container .room a").attr('class', 'desel');
            $(this).attr('class', 'sel');
            changeRoom(this);
            generateCanvas();
        }
    });
});

function checkIsDefaultFloor(){
    var isDefaultFloor = $("#floor_materials .item .sel").attr('id')=="floor_default";
    return isDefaultFloor;
}

// room selection clicked - floor
$(document).ready(function(){
    $("#floor_materials .item a").click(function(){
        if($(this).attr('class')=="desel") {
            $("#floor_materials .item a").attr('class', 'desel');
            $(this).attr('class', 'sel');
            var id = $(this).attr('id');
            if(checkIsDefaultFloor()){
                changeFloor($("#door_materials .item .sel"));
            }else{
                changeFloor(this);
            }
            generateCanvas();
        }
    });
});

    // section animation

function hideLeftSide(leftSide){
    if(!rightSectionOpen || leftSectionOpen){
        leftSide.addClass('disappear');
    }else{
        leftSide.removeClass('disappear');
    }
}

$(document).ready(function(){
    var leftSide = $(".left_side"),
    doorSection = $(".door_selection_container.selection_container"),
    doorBtn = $("#door_section_openclose_btn .openclose_btn_img"),
    roomSection = $(".room_selection_container.selection_container"),
    roomBtn = $("#room_section_openclose_btn .openclose_btn_img");

    function moveLeft(){
        roomSection.toggleClass('moveLeft');
		
		if(leftSectionOpen){
            leftSectionOpen = !leftSectionOpen;
            roomSection.css( "overflow", "initial" );
		}else{
			setTimeout(function(){
                roomSection.css( "overflow", "" );
				leftSectionOpen = !leftSectionOpen;
				roomSection.scroll();
			}, hideDuration)
		}
		       
        roomSection.scroll();
    
        var btn = roomSection.find('.section_openclose_btn');
        //btn.css('margin-right', '0');
        setTimeout(function(){
            btn.css('margin-right', '');
        }, hideDuration)
    
        setTimeout(function(){
            roomBtn.toggleClass('rotateRight');
            hideLeftSide(leftSide);
        }, 450)
    }
    
    function moveRight(){
        doorSection.toggleClass('moveRight');
		
		if(rightSectionOpen){
            rightSectionOpen = !rightSectionOpen;
            doorSection.css( "overflow", "initial" );
            setTimeout(function(){
                hideLeftSide(leftSide);
            }, 450)  
		}else{
			setTimeout(function(){
                doorSection.css( "overflow", "" );
                rightSectionOpen = !rightSectionOpen;
                hideLeftSide(leftSide);
				doorSection.scroll();
			}, hideDuration)
        }
        
        doorSection.scroll();	
		
        setTimeout(function(){
            doorBtn.toggleClass('rotateLeft');
        }, 450)  
    }

    $("#door_section_openclose_btn").click(function(){
        moveRight();
    });

    $("#room_section_openclose_btn").click(function(){
        moveLeft();
    });

    //swipe right
    $(".door_selection_container").swipe( {
        swipeRight:function(event, direction, distance, duration, fingerCount) {
            if(rightSectionOpen){
                moveRight();
            }
        }
    });
    //swipe left
    $(".door_selection_container").swipe( {
        swipeLeft:function(event, direction, distance, duration, fingerCount) {
            if(!rightSectionOpen){
                moveRight();
            }
        }
    });

    //swipe left
    $(".room_selection_container").swipe( {
        swipeLeft:function(event, direction, distance, duration, fingerCount) {
            if(leftSectionOpen){
                moveLeft();
            }
        }
    });
    //swipe right
    $(".room_selection_container").swipe( {
        swipeRight:function(event, direction, distance, duration, fingerCount) {
            if(!leftSectionOpen){
                moveLeft();
            }
        }
    });

    $(".selection_container").scroll(function(){
        var fromTop = $(this).scrollTop();
        var btn = $(this).find(".section_openclose_btn");
        var id = btn.attr('id').split('_')[0];
        
        if(id=="room" && !leftSectionOpen){
            btn.css("top", fromTop + "px");
        }else if(id=="door" && !rightSectionOpen){
            btn.css("top", fromTop + "px");
        }else{
            btn.css("top", '');
        }
        
     });
});

//under-door text position
function updateTextAndLoadingPosition(){
    var underDoorElement = $('#under_door');
    var loadingElement = $('#loading .loading_img');

    var windowHeight = document.body.clientHeight;
    var windowWidth = document.body.clientWidth;

    var windowRatio = windowHeight/windowWidth;

    if(windowRatio >= imageRatio){
        var widthtOfImg = windowHeight/imageRatio;
        var widthOfDoor = widthtOfImg * 0.135;
        var leftPosition = widthtOfImg * 0.08;
        underDoorElement.css('width',widthOfDoor+'px');

        underDoorElement.css('top','');
        underDoorElement.css('left',leftPosition + 'px');
        underDoorElement.css('bottom','');

        loadingElement.css('width',widthOfDoor + 'px');
        loadingElement.css('margin-left',(leftPosition+15) + 'px');
 
    } else{
        //var imageMinHeight = windowWidth * imageRatio;
        //underDoorElement.css('top',imageMinHeight*0.88 + 'px');
        var widthOfDoor = windowWidth * 0.135;
        underDoorElement.css('width',widthOfDoor+'px');
        underDoorElement.css('top','unset');
        underDoorElement.css('left','');
        underDoorElement.css('bottom','20px');

        loadingElement.css('width',widthOfDoor + 'px');
        loadingElement.css('margin-left','26%');
    }

    fitty('.under_door .price_container',{
        minSize: 14   
    });
    fitty('#under_door_desc',{
        minSize: 11   
    });
    
}

function showAPNGIfPossible(){
    if(supportAPNG()){
        var element = $("#loading img");
        var src = element.attr('src').split('.');
        var newSrc = src.splice(0,src.length-1).join(".") + ".png";
        element.attr('src', newSrc);
    }
}

//on load

$(document).ready(function(){
    showMobileAlert();

    showAPNGIfPossible();

    hideDuration = parseFloat($('.selection_container').css('transition-duration'))*1000;

    updateTextAndLoadingPosition();

    if(checkIsDefaultFloor()){
        changeFloor($("#door_materials .item .sel"));
    }
    generateCanvas();
    updateCanvasSize();

    $("textarea").on("input", function () {
        this.style.height = "auto";
        this.style.height = (this.scrollHeight+2) + "px";
    });
});

//on resize

$(window).resize(function() {
    updateCanvasSize();
    updateTextAndLoadingPosition();
});