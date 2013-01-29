// Order:default - výběr dodání na jinou adresu
$(document).ready(function(){
    $("#hide").css("display","none");
            
    $(".isFacture").click(function(){
        if ($('input[name=facture]:checked').val() == "checked" ) {
            $("#hide").slideDown("slow"); //Slide Down Effect   
        } else {
            $("#hide").slideUp("slow");	//Slide Up Effect
        }
    });            
});

// Order:paymentDelivery - výběr dopravy a podle toho platby
//delivery-0 = post
//delivery-1 = dpd
//delivery-2 = personally

//payment-0 = directDebit
//payment-1 = cash
//payment-2 = casOnDelivery
function toggleStatus() {
    if ($('#delivery-0').is(':checked')) {
        $('#elementsToOperateOn :input').removeAttr('disabled');
        if($('#payment-1').is(':checked')) {
            $('#payment-2').attr('checked', 'checked');
        }
        $('#payment-1').attr('disabled', true);
        
    } else if ($('#delivery-1').is(':checked')){
        $('#elementsToOperateOn :input').removeAttr('disabled');
        if($('#payment-1').is(':checked')) {
            $('#payment-0').attr('checked', 'checked');
        }
        else if($('#payment-2').is(':checked')) {
            $('#payment-0').attr('checked', 'checked');
        }
        $('#payment-1').attr('disabled', true);
        $('#payment-2').attr('disabled', true);
        
    } else if ($('#delivery-2').is(':checked')){
        $('#elementsToOperateOn :input').removeAttr('disabled');
        if($('#payment-2').is(':checked')) {
            $('#payment-1').attr('checked', 'checked');
        }
        $('#payment-2').attr('disabled', true);
    }
}

$(document).ready(function(){
    //  When user clicks on tab, this code will be executed
    $("#tabs li").click(function() {
        //  First remove class "active" from currently active tab
        $("#tabs li").removeClass('active');
 
        //  Now add class "active" to the selected/clicked tab
        $(this).addClass("active");
 
        //  Hide all tab content
        $(".tab_content").hide();
 
        //  Here we get the href value of the selected tab
        var selected_tab = $(this).find("a").attr("href");
 
        //  Show the selected tab content
        $(selected_tab).fadeIn();
 
        //  At the end, we add return false so that the click on the link is not executed
        return false;
    });
});

$(document).ready(function() {
        $().piroBox_ext({
        piro_speed : 700,
                bg_alpha : 0.5,
                piro_scroll : true // pirobox always positioned at the center of the page
        });
});
