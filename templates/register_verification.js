

$('#sendVerification').click(function(e){	

	var req = new Object();
	req["dstaddr"] = $("#phone").val();
	
	$.ajax( {
		url:"sendVerification.php",
		method: "POST",
		dataType:"text",
		//dataType:"json",
		data:{request:req}
	} )
	.done(function(msg){
		//alert(msg);
	})
	.fail(function(jqXHR, textStatus, errorThrown){
		console.log(textStatus, errorThrown);
	})
	.always(function(){
		//$("#debug_text").val( window.page_ordertime + "...done" );
	})
	;
});
