$(function(){
    $("input[name=re_password]").keyup(function(){
        if($("input[name=re_password]").val()==""){
            $(this).next('.validate_box').remove();
            $(this).after("<span class=\"validate_box\"><span></span></span>");
        }else if($("input[name=re_password]").val()==$("input[name=password]").val()){
            $(this).next('.validate_box').remove();
            $(this).after("<span class=\"validate_box validate_ok\"><span>OK</span></span>");
        }else{
            $(this).next('.validate_box').remove();
            $(this).after("<span class=\"validate_box validate_no\"><span>Password don't match</span></span>");
            
        }
    });
});

$(function(){
    $("input[name=email]").blur(function(){
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        var email= $(this).val();
        
        if(reg.test(email) === false) {
           $(this).next('.validate_box').remove();
           $(this).after("<span class=\"validate_box validate_no\"><span>Invalid email address</span></span>");
        }else{
            $(this).next('.validate_box').remove();
            $(this).after("<span class=\"validate_box validate_ok\"><span>OK</span></span>");
        }

    });
});

$(function(){
    $("input[name=website]").blur(function(){
        var reg = /^(http|https|ftp):\/\/([a-z0-9\-]+\.)*[a-z0-9][a-z0-9\-]+[a-z0-9](\.[a-z]{2,4})+$/i;
        var website= $(this).val();
        if(website!="http://"){
            if(reg.test(website) === false) {
               $(this).next('.validate_box').remove();
               $(this).after("<span class=\"validate_box validate_no\"><span>Invalid wesite address</span></span>");
            }else{
                $(this).next('.validate_box').remove();
                $(this).after("<span class=\"validate_box validate_ok\"><span>OK</span></span>");
            }
        }else{
            $(this).next('.validate_box').remove();
        }
    });
});

$(function(){
    $("input[name=username]").blur(function(){
        if($(this).val().length < 5) {
           $(this).next('.validate_box').remove();
           $(this).after("<span class=\"validate_box validate_no\"><span>The username should be at least five characters long</span></span>");
        }else{
            $(this).next('.validate_box').remove();
            $(this).after("<span class=\"validate_box validate_ok\"><span>OK</span></span>");
        }
    });
});

