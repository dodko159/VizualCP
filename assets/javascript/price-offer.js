$(document).ready(function(){
    var priceOfferWrap = $('#price-offer-wrapper');

    $('#po_link').click(function(){
        $(priceOfferWrap).toggleClass("hide");
        $("#price-offer-window").removeClass("hide");
        $("#price-offer-add-item-window").addClass("hide");

        $('.price-offer-message').each(function( index ) { //skovaj spravy
            if(!$( this ).hasClass('hide'))
                $( this ).addClass('hide');
        });
    });

    $('#po_link_add').click(function(){
        //resetovat data
        $("#price-offer-width-add").val("W60");
        $("#price-offer-count-add").val("1");
        $("#price-offer-frame-add").prop('checked', true);
        $("#price-offer-info-add").val("");
        $("#price-offer-add-title").html(getActualDoorAsTitle());

        //nastavenie viditelnosti
        $(priceOfferWrap).removeClass("hide");
        $("#price-offer-window").addClass("hide");
        $("#price-offer-add-item-window").removeClass("hide");
    });

    $("#price-offer-add-item-window form").submit(function ( e ) {
        e.preventDefault(); // zrusi klasicky submit

        var actualDoor = getActualDoor();
        if (actualDoor.length === 2) {
            var frame = 0;
            if($("#price-offer-frame-add").is(':checked')){
                frame = 1;
            }
            var data = {
                'doorType' : actualDoor[0],
                'material' : actualDoor[1],
                'width'    : $("#price-offer-width-add").val(),
                'count'    : $("#price-offer-count-add").val(),
                'frame'    : frame,
                'assembly' : true,
                'info'     : $("#price-offer-info-add").val(),
                'addDoor' : true
            }
            $.post("add-session-val.php", data)
                .done( function(response){
                    if(isSuccess(response)){
                        pridajElement(response);
                    } else {
                        console.log(json['message']);
                        // hodi error
                    }
                }).fail(
                    // hodi error
                ).always(function(){
                    $('#price-offer-add-close-button').click(); // zatvor
                });
        }
    });

    $("#price-offer-add-item-window .fa-minus").click(function(){
        var cntInput = $("#price-offer-count-add")[0];
        if (cntInput.value > 1) {
            cntInput.value--;
        }
    });
    $("#price-offer-add-item-window .fa-plus").click(function(){
        var cntInput = $("#price-offer-count-add")[0];
        cntInput.value++;
    });

    $('#price-offer-close-button').click(function(){
        $(priceOfferWrap).addClass("hide");
        grecaptcha.reset(); // reset recaptcha
    });

    $('#price-offer-add-close-button').click(function(){
        $(priceOfferWrap).addClass("hide");
        grecaptcha.reset(); // reset recaptcha
    });
   
    $("#price-offer-window #priceOfferMailForm").submit(function ( e ) {
        e.preventDefault(); // zrusi klasicky submit

        if(validateOfferForm()){
            console.log("Sending...");
            sendMail();
        }

    });

    $("#price-offer-window #priceOfferPDFform").submit(function ( e ) {
        e.preventDefault(); // zrusi klasicky submit
        var data = {
            'name': $('#price-offer-name').val()
        };
        console.log("priceOfferPDFform CLICK");
        $.ajax({                    // stiahni PDF
            url: 'downloadPDF.php', 
            data: data,
            type: 'POST',
            success: function (resp) {
                // For Notification
                console.log(resp);
                //let respObj = JSON.parse(resp2);
                if(resp.success){
                    // nic
                } else {
                    console.log("Download fail");
                }
            }
        });
    });

    $('#price-offer-assembly').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        $.post("add-session-val.php", {
            'function' : "setAssembly",
            'newValue' : checked
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $('#price-offer-seal').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        $.post("add-session-val.php", {
            'function' : "setSeal",
            'newValue' : checked
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $('#price-offer-putty').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        $.post("add-session-val.php", {
            'function' : "setPutty",
            'newValue' : checked
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $('#price-offer-ironFrame').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        $.post("add-session-val.php", {
            'function' : "setIronFrame",
            'newValue' : checked
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $('#price-offer-floor3').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        $.post("add-session-val.php", {
            'function' : "setFloor3",
            'newValue' : checked
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $("#price-offer-thickerFrame").on( "focusout", function() {
        var pocet = null;
        if($(this).val() > 0){
            pocet = $(this).val();
        }
        $.post("add-session-val.php", {
            'function' : "setThickerFrame",
            'newValue' : pocet
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $("#price-offer-higherFrame").on( "focusout", function() {
        var pocet = null;
        if($(this).val() > 0){
            pocet = $(this).val();
        }
        $.post("add-session-val.php", {
            'function' : "setHigherFrame",
            'newValue' : pocet
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $("#price-offer-doorLiners").on( "focusout", function() {
        var pocet = null;
        if($(this).val() > 0){
            pocet = $(this).val();
        }
        $.post("add-session-val.php", {
            'function' : "setDoorLiners",
            'newValue' : pocet
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $("#price-offer-distance").on( "focusout", function() {
        var pocet = null;
        if($(this).val() > 0){
            pocet = $(this).val();
        }
        $.post("add-session-val.php", {
            'function' : "setDistance",
            'newValue' : pocet
        }).done(function (response) {
            if(isSuccess(response)){
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $('input.noBellowZero').keyup(function() {
        if($(this).val() < 0){
            $(this).val("0");
        }
    });

    $('#price-offer-name').change(function() {
        $.post("add-session-val.php", {'name':this.value});
    });
    $('#price-offer-mail').change(function() {
        $.post("add-session-val.php", {'mail':this.value});
    });
    $('#price-offer-mobile').change(function() {
        $.post("add-session-val.php", {'phone':this.value});
    });

    setListeners("#price-offer-window ");
    recalculatePriceOfferSize();
});

$(window).resize(function() {
    recalculatePriceOfferSize();
});

function setListeners(id){

    $(id+'.price-offer-remove-item').click(function(){
        var poLine = $( this ).parent().parent();
        var id = $( poLine ).attr("door-id");
        $.post("add-session-val.php", {
            'function' : "remove",
            'position' : id
        }).done(function (response) {
            if(isSuccess(response)){
                $("#po-item-"+id).remove();
                if($("#price-offer-items-container").children().length == 0 ){
                    $("#price-offer-empty-text").removeClass('hide');
                    $('#price-offer-additional').addClass('hide');
                }
                resetFullPrice();
            }
        });
    });

    $(id+'.price-offer-width').on('change', function() {
        var poLine = $( this ).parent();
        var id = $( poLine ).attr("door-id");
        $.post("add-session-val.php", {
            'function' : "changeWidth",
            'newValue' : this.value,
            'position' : id
        }).done(function () {
            console.log("sucess");
            // TODO asi nic .. ale na fail by mohlo
        });
    });

    $(id+'.fa-clone').click( function() {
        var poLine = $( this ).parent().parent();
        var id = $( poLine ).attr("door-id");
        $.post("add-session-val.php", {
            'function' : "clone",
            'position' : id
        }).done(function (response) {
            if(isSuccess(response)){
                pridajElement(response);
            } else {
                console.log(json['message']);
                // hodi error
            }
        });
    });

    $(id+'.fa-minus').click( function() {
        var poLine = $( this ).parent();
        var cntInput = $( poLine ).children(".price-offer-count")[0];
        if (cntInput.value > 1) {
            cntInput.value--;
            $( cntInput ).change();
        }
    });
    $(id+'.fa-plus').click( function() {
        var poLine = $( this ).parent();
        var cntInput = $( poLine ).children(".price-offer-count")[0];
        cntInput.value++;
        $( cntInput ).change();
    });

    $(id+'.price-offer-count').change(function() {
        var poLine = $( this ).parent();
        var id = $( poLine ).attr("door-id");
        $.post("add-session-val.php", {
            'function' : "changeCount",
            'newValue' : this.value,
            'position' : id
        }).done(function (response) {
            if(isSuccess(response)){
                getPriceOf(id);
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $(id+'.price-offer-info').change(function() {
        var poLine = $( this ).parent();
        var id = $( poLine ).attr("door-id");
        $.post("add-session-val.php", {
            'function' : "changeInfo",
            'newValue' : this.value,
            'position' : id
        }).done(function (response) {
            if(isSuccess(response)){
                
            } else {
                // hodi error
            }
        });
    });

    $(id+'.price-offer-frame').click(function() {
        var poLine = $( this ).parent().parent();
        var id = $( poLine ).attr("door-id");
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        $.post("add-session-val.php", {
            'function' : "changeFrame",
            'newValue' : checked,
            'position' : id
        }).done(function (response) {
            if(isSuccess(response)){
                getPriceOf(id);
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });

    $(id+'.price-offer-assemble').click(function() {
        var poLine = $( this ).parent().parent();
        var id = $( poLine ).attr("door-id");
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        $.post("add-session-val.php", {
            'function' : "changeAssemble",
            'newValue' : checked,
            'position' : id
        }).done(function (response) {
            if(isSuccess(response)){
                getPriceOf(id);
                resetFullPrice();
            } else {
                //todo chyba
            }
        });
    });
}

function resetFullPrice() {
    $.get( "./cart-get.php?fullPrice=1", function( data ) {
        var price = 0;
        if(isSuccess(data)){
            var json = JSON.parse(data);
            price = json['data'];
        } else {
            //todo hod chybu
        }
        $("#price-offer-full-price-number").html(price);
    });
}

function getPriceOf(id){
    $.get( "./cart-get.php?price="+id, function( data ) {
        var price = 0;
        if(isSuccess(data)){
            var json = JSON.parse(data);
            price = json['data'];
        } else {
            //todo hod chybu
        }
        $('#po-item-'+id+" .price-offer-item-price-number").html(price);
    });
}

function pridajElement(res){
    var json = JSON.parse(res);
    $('#price-offer-items-container').append(json['result']);
    var newItem = $('#price-offer-items-container .price-offer-line-item').last();
    var id = "#" + $(newItem).attr('id') + " ";
    setListeners(id);
    //zmaz prazdny oznam
    $('#price-offer-empty-text').addClass('hide');
    $('#price-offer-additional').removeClass('hide');
    resetFullPrice();
}

function isSuccess(res){
    var json = JSON.parse(res);
    return json != null && json['sucess'] == true;
}

function getActualDoor() {
    var result = [];
    var id = '#selected_door';
    var src = $(id).attr('src');
    var fullName = src.split("/").splice(-1,1)[0];
    result.push(fullName.split(".")[0]);
    id = '#selected_material';
    src = $(id).attr('src');
    fullName = src.split("/").splice(-1,1)[0];
    result.push(fullName.split(".")[0]);
    return result;
}

function getActualDoorAsTitle() {
    var selDoor = $("#doors .sel").parent();
    var doorName = $(selDoor).children(".door_name").html();
    var selMat = $("#door_materials .sel").parent();
    var matName = $(selMat).children(".material_name").html();

    var result = doorName.toUpperCase() + " - " + matName;
    return result;
}

function recalculatePriceOfferSize(){
    var wh = $("#price-offer-window").height();
    var title = $("#price-offer-title");
    var th = title.outerHeight() + parseInt($( title ).css('marginTop')) + parseInt($( title ).css('marginBottom'));
    var newHeight = wh - th;
    $("#price-offer-content").css("height", newHeight+"px");
}

/* function sendSessionValues(sessionData) {

    var data = {
        'material': "testovaci material"
    };

    $.ajax({                    // add session data
        url: 'add-session-val.php', 
        data: data,
        type: 'POST',
        success: function (resp2) {
            // For Notification
            console.log(resp2);
            if(resp2.success){
                console.log("nastavene");
            }else{
                console.log("chyba");
            }
        }
    });
} */

function sendMail(){
    var data = {
        'name': $('#price-offer-name').val(),
        'email': $('#price-offer-mail').val(),
        'contact': $('#price-offer-mobile').val(),
        'message' : $('#price-offer-note').val()
    };

    $.ajax({                    // send mail
        url: 'offerMail.php', 
        data: data,
        type: 'POST',
        success: function (resp2) {
            // For Notification
            console.log(resp2);
            //let respObj = JSON.parse(resp2);
            if(resp2.success){
                console.log("Sucess");
                $( "#price-offer-successMessage1" ).removeClass('hide');
            }else{
                console.log("Fail");
                $( "#price-offer-errorMessage3" ).removeClass('hide');
            }
        }
    });
}

function validateOfferForm(){
    var toCheck = ["mail","name"];
    var ret = true;
    for(var i = 0 ; i < toCheck.length; i++){
        if(!validateOfferValue(toCheck[i], i>0))
            ret = false;
    }

    /*if(ret & & $(".g-recaptcha").length){
        var rcpResponse = $('#g-recaptcha-response').val();

        if(rcpResponse === ""){
            $( '#reCaptcha-errorMessage1' ).removeClass('hide');
            return false;
        }

        var data = {
            'g-recaptcha-response' : rcpResponse
        };

        $.ajax({   // check captcha
            url: 'captcha-check.php', 
            data: data,
            type: 'POST',
            success: function (resp1) {
                grecaptcha.reset(); // reset recaptcha
                var obj = jQuery.parseJSON( resp1 );
                if(!obj.response.success){ // if not success
                    $( '#reCaptcha-errorMessage2' ).removeClass('hide');
                }else{
                    sendMail();
                }
            }
        });
    }else{ */
        return ret;
    //}
}

function validateOfferValue(type){
    return validateOfferValue(type, false);
}

function validateOfferValue(type, keepError){
    const reqErr = "#price-offer-errorMessage1";
    const mailErr = "#price-offer-errorMessage2";

      $('.price-offer-message').each(function( index ) {
        if(!$( this ).hasClass('hide') && !keepError)
            $( this ).addClass('hide');
      });

    if(type == 'name'){ // kontrola mena
        var name = $('#price-offer-name');
        if(isNullOrEmpty($(name).val())){
            if(!$(name).hasClass('invalid-value'))
                $(name).addClass('invalid-value');
            $( reqErr ).removeClass('hide'); // req error
            return false;
        }else{
            $(name).removeClass('invalid-value');
            return true;
        }
    }else if(type == 'mail'){   // kontrola emailu
        var mail = $('#price-offer-mail');
        if(isNullOrEmpty($(mail).val())){
            if(!$(mail).hasClass('invalid-value'))
                $(mail).addClass('invalid-value');
            $( reqErr ).removeClass('hide'); // req error
            return false;
        }else if(!isEmail($(mail).val())){
            if(!$(mail).hasClass('invalid-value'))
                $(mail).addClass('invalid-value');
            $( mailErr ).removeClass('hide'); // mail error
            return false;
        }else{
            $(mail).removeClass('invalid-value');
            return true;
        }
    }/* else if(type == 'phone'){ // kontrolo rozlohy
        var phone = $('#price-offer-mobile');
        if(isNullOrEmpty($(phone).val())){
            if(!$(phone).hasClass('invalid-value'))
                $(phone).addClass('invalid-value');
            $( reqErr ).removeClass('hide'); // req error
            return false;
        }else{
            $(phone).removeClass('invalid-value');
            return true;
        }
    } */
    return true;
}

function isEmail(email) {
    if(email == null)
        return false;

    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function isNullOrEmpty(text){
    if(text == null || text.trim() == "")
        return true;
    return false;
}