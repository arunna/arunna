$(document).ready(function(){
                    //Examples of how to assign the ColorBox event to elements
                    $("a[rel='colorbox_elastic']").colorbox();
                    $("a[rel='colorbox_fade']").colorbox({transition:"fade"});
                    $("a[rel='colorbox_notrans']").colorbox({transition:"none", width:"75%", height:"75%"});
                    $("a[rel='colorbox_slideshow']").colorbox({slideshow:true});
                    $(".colorbox_outside_html").colorbox();
                    $(".colorbox_outside_flash").colorbox({iframe:true, innerWidth:700, innerHeight:344});
                    $(".colorbox_webpage").colorbox({width:"80%", height:"80%", iframe:true});
                    $(".upload_image").colorbox({width:"70%", height:"98%", iframe:true});
                    $(".upload_flash").colorbox({width:"70%", height:"98%", iframe:true});
                    $(".upload_video").colorbox({width:"70%", height:"98%", iframe:true});
                    $(".upload_music").colorbox({width:"70%", height:"98%", iframe:true});
                    $(".upload_pdf").colorbox({width:"70%", height:"98%", iframe:true});
                    $(".upload_doc").colorbox({width:"70%", height:"98%", iframe:true});
                    $(".openinviter").colorbox({width:"55%", height:"90%", iframe:true});
                    $(".colorbox_inline_html").colorbox({width:"50%", inline:true, href:"#inline_example1"});
                    $(".colorbox_inaction").colorbox({
                            onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                            onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                            onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                            onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                            onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                    });
                    
                    //Example of preserving a JavaScript event for inline calls.
                    $("#click").click(function(){ 
                            $('#click').css({"background-color":"#f00", "color":"#000", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
                            return false;
                    });
                    
                    
            });
