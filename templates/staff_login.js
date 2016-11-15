

$("#enablepass").change(function(){
	if(this.checked){
			$("#upass").show();
			$("#uinfo").hide();
	}
	else{
			$("#upass").hide();
			$("#uinfo").show();
	}
});

$("#uinfo").hide();
