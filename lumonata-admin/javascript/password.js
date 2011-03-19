$(document).ready( function(){
    $("#password").passStrength({
            shortPass: 		"shortPass",
            badPass:		"badPass",
            goodPass:		"goodPass",
            strongPass:		"strongPass",
            baseStyle:		"validate_box",
            userid:		"#username"
           
    });
    
});