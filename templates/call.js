$(document).ready(function(){
  $('#submit').on('click', function() {
  	// configure a request array
  	var req = [];
    $('input').each(function() {
    	// find the item
			var item = $(this).parent().parent().find('input[type=number]');
			// ingredient name
			var name = $(this).parent().parent().find("td:first").text();
			// non zero and non empty
    	if (item.val() != "" && item.val() != "0") {
					// configure a new request
				var _req = {};
				_req = {
					"ingredient": name,
					"num": item.val(),
					"unit": item.parent().text().replace(/\s+/g, '') // remove space
				}
				req.push(_req);
			}
			// clear input
			item.val('');
    });

    if (req.length == 0) {
    	alert('請至少選取一項補料，且數量大於０')
    	return;
    }

  	console.log(JSON.stringify(req));

  	// ----- send calls to call_process.php -----
	 	$.ajax( {
			url: "call_process.php",
			method: "POST",
			dataType: "text",
			data: {request: req}
		} )
		.done(function(msg){
			console.log('Submit a call!');
			alert('已經叫料');
			// setTimeout(function(){ alert('已經叫料'); }, 1000);
		})
		.fail(function(jqXHR, textStatus, errorThrown){
			console.log(textStatus, errorThrown);
			alert('叫料失敗！')
		})
		.always(function(){

		});

  });
});