$(document).ready(function(){
		// ------- START navbar setting ------------------------------------------------------
        $("#nav_cross").click(function(){

            $(".w3-sidenav").toggle();
            $("#main").css({"marginLeft":"0%"});
            $(".w3-sidenav").css({"display":"none"});
            $(".w3-opennav").css({"display":"inline-block"});

        });

        $("#nav_open").click(function(){
                            
            $(".w3-sidenav").toggle();
            $("#main").css({"marginLeft":"20%"});
            $(".w3-sidenav").css({"display":"block", "width":"20%"});
            $(".w3-opennav").css({"display":"none"});

        });


        $("#nav_open").css({"position":"fixed"});
        /////
        $("#open_cart").css({"position":"fixed", "right":"7%", "bottom":"7%", "z-index":"2"});
        $("#open_cart2").css({"position":"fixed", "right":"15%", "bottom":"7%", "z-index":"2"});
        $("#open_cart3").css({"position":"fixed", "right":"23%", "bottom":"7%", "z-index":"2"});
        $("#open_cart4").css({"position":"fixed", "right":"31%", "bottom":"7%", "z-index":"2"});
        //////
        $("#nav_cross").click(); 
		// ------- END navbar setting ------------------------------------------------------
				
});