const phpUrl = "add-session-val.php";

window.jQuery(document).ready(function(){
    var isOldPriceOfferEnabled = false;
    var priceOfferOpenByDefault = false;
    var priceOfferWrap = window.jQuery('#price-offer-wrapper');

    if (!priceOfferOpenByDefault) {
        window.jQuery("#price-offer-container").addClass("display-none");
        window.jQuery("#doors").removeClass("display-none");
    }

    document.getElementById('openclose_btn_text_area_doors').addEventListener('click', function () {
        window.jQuery("#price-offer-container").addClass("display-none");
        window.jQuery("#doors-container").removeClass("display-none");
    })

    document.getElementById('openclose_btn_text_area_priceOffer').addEventListener('click', function () {
        window.jQuery("#price-offer-container").removeClass("display-none");
        window.jQuery("#doors-container").addClass("display-none");
    })

    //open shopping cart
    window.jQuery('#po_link').click(function(){
        window.jQuery(priceOfferWrap).toggleClass("hide");
        window.jQuery("#price-offer-window").removeClass("hide");
        window.jQuery("#price-offer-add-item-window").addClass("hide");

        window.jQuery('.price-offer-message').each(function( index ) { //skovaj spravy
            if(!window.jQuery( this ).hasClass('hide'))
                window.jQuery( this ).addClass('hide');
        });
    });

    //original version
    //intermediate window before adding to the shopping cart
    if (isOldPriceOfferEnabled) {
        // window.jQuery('#po_link_add').click(function () {
        //     //resetovat data
        //     window.jQuery("#price-offer-width-add").val("W60");
        //     window.jQuery("#price-offer-count-add").val("1");
        //     window.jQuery("#price-offer-frame-add").prop('checked', true);
        //     window.jQuery("#price-offer-info-add").val("");
        //     window.jQuery("#price-offer-add-title").html(getActualDoorAsTitle());
        //
        //     //nastavenie viditelnosti
        //     window.jQuery(priceOfferWrap).removeClass("hide");
        //     window.jQuery("#price-offer-window").addClass("hide");
        //     window.jQuery("#price-offer-add-item-window").removeClass("hide");
        // });
    }

    //original version
    if (isOldPriceOfferEnabled) {
        // window.jQuery("#price-offer-add-item-window form").submit(function (e) {
        //     e.preventDefault(); // zrusi klasicky submit
        //
        //     var actualDoor = getActualDoor();
        //     if (actualDoor.length === 2) {
        //         var frame = 0;
        //         if (window.jQuery("#price-offer-frame-add").is(':checked')) {
        //             frame = 1;
        //         }
        //         var data = {
        //             'doorType': actualDoor[0],
        //             'material': actualDoor[1],
        //             'width': window.jQuery("#price-offer-width-add").val(),
        //             'count': window.jQuery("#price-offer-count-add").val(),
        //             'frame': frame,
        //             'assembly': true,
        //             'info': window.jQuery("#price-offer-info-add").val(),
        //             'addDoor': true
        //         }
        //         window.jQuery.post(phpUrl, data)
        //             .done(function (response) {
        //                 if (isSuccess(response)) {
        //                     pridajElement(response);
        //                 } else {
        //                     console.log(json['message']);
        //                     // hodi error
        //                 }
        //             }).fail(
        //             // hodi error
        //         ).always(function () {
        //             window.jQuery('#price-offer-add-close-button').click(); // zatvor
        //         });
        //     }
        // });
    }

    //updated version
    if (!isOldPriceOfferEnabled) {
        window.jQuery("#po_link_add").click(function (e) {
            e.preventDefault(); // zrusi klasicky submit

            var actualDoor = getActualDoor();
            if (actualDoor.length === 2) {
                // var frame = 0;
                // if(window.jQuery("#price-offer-frame-add").is(':checked')){
                //     frame = 1;
                // }
                var data = {
                    'doorType': actualDoor[0],
                    'material': actualDoor[1],
                    'width': null,
                    'count': 1,
                    'frame': true,
                    'assembly': false,
                    'info': "",
                    'addDoor': true
                }
                window.jQuery.post(phpUrl, data)
                    .done(function (response) {
                        if (isSuccess(response)) {
                            pridajElement(response);
                        } else {
                            console.log(json['message']);
                            // hodi error
                        }
                    }).fail(
                    // hodi error
                ).always(function () {
                    window.jQuery('#price-offer-add-close-button').click(); // zatvor
                });
            }
        });
    }

    if (isOldPriceOfferEnabled) {
        // window.jQuery("#price-offer-add-item-window .fa-minus").click(function () {
        //     var cntInput = window.jQuery("#price-offer-count-add")[0];
        //     if (cntInput.value > 1) {
        //         cntInput.value--;
        //     }
        // });
        // window.jQuery("#price-offer-add-item-window .fa-plus").click(function () {
        //     var cntInput = window.jQuery("#price-offer-count-add")[0];
        //     cntInput.value++;
        // });
    }

    window.jQuery('#price-offer-close-button').click(function(){
        window.jQuery(priceOfferWrap).addClass("hide");
        grecaptcha.reset(); // reset recaptcha
    });

    window.jQuery('#price-offer-add-close-button').click(function(){
        window.jQuery(priceOfferWrap).addClass("hide");
        grecaptcha.reset(); // reset recaptcha
    });

    window.jQuery("#price-offer-window #priceOfferMailForm").submit(function ( e ) {
        e.preventDefault(); // zrusi klasicky submit

        if(validateOfferForm()){
            console.log("Sending...");
            sendMail();
        }

    });

    window.jQuery("#price-offer-window #priceOfferPDFform").submit(function ( e ) {
        e.preventDefault(); // zrusi klasicky submit
        var data = {
            'name': window.jQuery('#price-offer-name').val()
        };
        console.log("priceOfferPDFform CLICK");
        window.jQuery.ajax({                    // stiahni PDF
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

    window.jQuery('#price-offer-assembly').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery('#price-offer-seal').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery('#price-offer-putty').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery('#price-offer-ironFrame').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery('#price-offer-floor3').click(function() {
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery("#price-offer-thickerFrame").on( "focusout", function() {
        var pocet = null;
        if(window.jQuery(this).val() > 0){
            pocet = window.jQuery(this).val();
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery("#price-offer-higherFrame").on( "focusout", function() {
        var pocet = null;
        if(window.jQuery(this).val() > 0){
            pocet = window.jQuery(this).val();
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery("#price-offer-doorLiners").on( "focusout", function() {
        var pocet = null;
        if(window.jQuery(this).val() > 0){
            pocet = window.jQuery(this).val();
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery("#price-offer-distance").on( "focusout", function() {
        var pocet = null;
        if(window.jQuery(this).val() > 0){
            pocet = window.jQuery(this).val();
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery('input.noBellowZero').keyup(function() {
        if(window.jQuery(this).val() < 0){
            window.jQuery(this).val("0");
        }
    });

    window.jQuery('#price-offer-name').change(function() {
        window.jQuery.post(phpUrl, {'name':this.value});
    });
    window.jQuery('#price-offer-mail').change(function() {
        window.jQuery.post(phpUrl, {'mail':this.value});
    });
    window.jQuery('#price-offer-mobile').change(function() {
        window.jQuery.post(phpUrl, {'phone':this.value});
    });

    setListeners("#price-offer-window ");
    recalculatePriceOfferSize();
});

window.jQuery(window).resize(function() {
    recalculatePriceOfferSize();
});

function setListeners(id){

    window.jQuery(id+'.price-offer-remove-item').click(function(){
        var poLine = window.jQuery( this ).parent().parent();
        var id = window.jQuery( poLine ).attr("door-id");
        window.jQuery.post(phpUrl, {
            'function' : "remove",
            'position' : id
        }).done(function (response) {
            if(isSuccess(response)){
                window.jQuery("#po-item-"+id).remove();
                if(window.jQuery("#price-offer-items-container").children().length == 0 ){
                    window.jQuery("#price-offer-empty-text").removeClass('hide');
                    window.jQuery('#price-offer-additional').addClass('hide');
                }
                resetFullPrice();
            }
        });
    });

    window.jQuery(id+'.price-offer-width').on('change', function() {
        var poLine = window.jQuery( this ).parent();
        var id = window.jQuery( poLine ).attr("door-id");
        window.jQuery.post(phpUrl, {
            'function' : "changeWidth",
            'newValue' : this.value,
            'position' : id
        }).done(function () {
            console.log("sucess");
            // TODO asi nic .. ale na fail by mohlo
        });
    });

    window.jQuery(id+'.fa-clone').click( function() {
        var poLine = window.jQuery( this ).parent().parent();
        var id = window.jQuery( poLine ).attr("door-id");
        window.jQuery.post(phpUrl, {
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

    window.jQuery(id+'.fa-minus').click( function() {
        var poLine = window.jQuery( this ).parent();
        var cntInput = window.jQuery( poLine ).children(".price-offer-count")[0];
        if (cntInput.value > 1) {
            cntInput.value--;
            window.jQuery( cntInput ).change();
        }
    });
    window.jQuery(id+'.fa-plus').click( function() {
        var poLine = window.jQuery( this ).parent();
        var cntInput = window.jQuery( poLine ).children(".price-offer-count")[0];
        cntInput.value++;
        window.jQuery( cntInput ).change();
    });

    window.jQuery(id+'.price-offer-count').change(function() {
        var poLine = window.jQuery( this ).parent();
        var id = window.jQuery( poLine ).attr("door-id");
        window.jQuery.post(phpUrl, {
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

    window.jQuery(id+'.price-offer-info').change(function() {
        var poLine = window.jQuery( this ).parent();
        var id = window.jQuery( poLine ).attr("door-id");
        window.jQuery.post(phpUrl, {
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

    window.jQuery(id+'.price-offer-frame').click(function() {
        var poLine = window.jQuery( this ).parent().parent();
        var id = window.jQuery( poLine ).attr("door-id");
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        window.jQuery.post(phpUrl, {
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

    window.jQuery(id+'.price-offer-assemble').click(function() {
        var poLine = window.jQuery( this ).parent().parent();
        var id = window.jQuery( poLine ).attr("door-id");
        var checked = 0;
        if(this.checked){
            checked = 1;
        }
        window.jQuery.post(phpUrl, {
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
    window.jQuery.get( "./cart-get.php?fullPrice=1", function( data ) {
        var price = 0;
        if(isSuccess(data)){
            var json = JSON.parse(data);
            price = json['data'];
        } else {
            //todo hod chybu
        }
        window.jQuery("#price-offer-full-price-number").html(price);
    });
}

function getPriceOf(id){
    window.jQuery.get( "./cart-get.php?price="+id, function( data ) {
        var price = 0;
        if(isSuccess(data)){
            var json = JSON.parse(data);
            price = json['data'];
        } else {
            //todo hod chybu
        }
        window.jQuery('#po-item-'+id+" .price-offer-item-price-number").html(price);
    });
}

function pridajElement(res){
    var json = JSON.parse(res);
    window.jQuery('#price-offer-items-container').append(json['result']);
    var newItem = window.jQuery('#price-offer-items-container .price-offer-line-item').last();
    var id = "#" + window.jQuery(newItem).attr('id') + " ";
    setListeners(id);
    //zmaz prazdny oznam
    window.jQuery('#price-offer-empty-text').addClass('hide');
    window.jQuery('#price-offer-additional').removeClass('hide');
    resetFullPrice();
}

function isSuccess(res){
    var json = JSON.parse(res);
    return json != null && json['sucess'] == true;
}

function getActualDoor() {
    var result = [];
    var id = '#selected_door';
    var src = window.jQuery(id).attr('src');
    var fullName = src.split("/").splice(-1,1)[0];
    result.push(fullName.split(".")[0]);
    id = '#selected_material';
    src = window.jQuery(id).attr('src');
    fullName = src.split("/").splice(-1,1)[0];
    result.push(fullName.split(".")[0]);
    return result;
}

function getActualDoorAsTitle() {
    var selDoor = window.jQuery("#doors .sel").parent();
    var doorName = window.jQuery(selDoor).children(".door_name").html();
    var selMat = window.jQuery("#door_materials .sel").parent();
    var matName = window.jQuery(selMat).children(".material_name").html();

    var result = doorName.toUpperCase() + " - " + matName;
    return result;
}

function recalculatePriceOfferSize(){
    var wh = window.jQuery("#price-offer-window").height();
    var title = window.jQuery("#price-offer-title");
    var th = title.outerHeight() + parseInt(window.jQuery( title ).css('marginTop')) + parseInt(window.jQuery( title ).css('marginBottom'));
    var newHeight = wh - th;
    window.jQuery("#price-offer-content").css("height", newHeight+"px");
}

/* function sendSessionValues(sessionData) {

    var data = {
        'material': "testovaci material"
    };

    window.jQuery.ajax({                    // add session data
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
        'name': window.jQuery('#price-offer-name').val(),
        'email': window.jQuery('#price-offer-mail').val(),
        'contact': window.jQuery('#price-offer-mobile').val(),
        'message' : window.jQuery('#price-offer-note').val()
    };

    window.jQuery.ajax({                    // send mail
        url: 'offerMail.php',
        data: data,
        type: 'POST',
        success: function (resp2) {
            // For Notification
            console.log(resp2);
            //let respObj = JSON.parse(resp2);
            if(resp2.success){
                console.log("Sucess");
                window.jQuery( "#price-offer-successMessage1" ).removeClass('hide');
            }else{
                console.log("Fail");
                window.jQuery( "#price-offer-errorMessage3" ).removeClass('hide');
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

    /*if(ret & & window.jQuery(".g-recaptcha").length){
        var rcpResponse = window.jQuery('#g-recaptcha-response').val();

        if(rcpResponse === ""){
            window.jQuery( '#reCaptcha-errorMessage1' ).removeClass('hide');
            return false;
        }

        var data = {
            'g-recaptcha-response' : rcpResponse
        };

        window.jQuery.ajax({   // check captcha
            url: 'captcha-check.php', 
            data: data,
            type: 'POST',
            success: function (resp1) {
                grecaptcha.reset(); // reset recaptcha
                var obj = jQuery.parseJSON( resp1 );
                if(!obj.response.success){ // if not success
                    window.jQuery( '#reCaptcha-errorMessage2' ).removeClass('hide');
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

      window.jQuery('.price-offer-message').each(function( index ) {
        if(!window.jQuery( this ).hasClass('hide') && !keepError)
            window.jQuery( this ).addClass('hide');
      });

    if(type == 'name'){ // kontrola mena
        var name = window.jQuery('#price-offer-name');
        if(isNullOrEmpty(window.jQuery(name).val())){
            if(!window.jQuery(name).hasClass('invalid-value'))
                window.jQuery(name).addClass('invalid-value');
            window.jQuery( reqErr ).removeClass('hide'); // req error
            return false;
        }else{
            window.jQuery(name).removeClass('invalid-value');
            return true;
        }
    }else if(type == 'mail'){   // kontrola emailu
        var mail = window.jQuery('#price-offer-mail');
        if(isNullOrEmpty(window.jQuery(mail).val())){
            if(!window.jQuery(mail).hasClass('invalid-value'))
                window.jQuery(mail).addClass('invalid-value');
            window.jQuery( reqErr ).removeClass('hide'); // req error
            return false;
        }else if(!isEmail(window.jQuery(mail).val())){
            if(!window.jQuery(mail).hasClass('invalid-value'))
                window.jQuery(mail).addClass('invalid-value');
            window.jQuery( mailErr ).removeClass('hide'); // mail error
            return false;
        }else{
            window.jQuery(mail).removeClass('invalid-value');
            return true;
        }
    }/* else if(type == 'phone'){ // kontrolo rozlohy
        var phone = window.jQuery('#price-offer-mobile');
        if(isNullOrEmpty(window.jQuery(phone).val())){
            if(!window.jQuery(phone).hasClass('invalid-value'))
                window.jQuery(phone).addClass('invalid-value');
            window.jQuery( reqErr ).removeClass('hide'); // req error
            return false;
        }else{
            window.jQuery(phone).removeClass('invalid-value');
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