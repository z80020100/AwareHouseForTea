/* Edit Menu Function on jQuery v1.11.3 */
jQuery.ajaxSetup({async:false});

// Global Variables
var g_button_submit_bind_state = false;
var g_button_cancel_bind_state = false;
var g_click_series_s_id;
var g_click_main_m_id;

function road_main_by_s_id(){
    //console.log("Enter: road_main_by_s_id");
    $("#button_cancel").trigger('click');
    var series_s_id = $(this).data('series_s_id');
    g_click_series_s_id = series_s_id;
    console.log('series_s_id = ' + series_s_id);
    $.ajax({
        url:"edit_menu_op.php",
        method:"POST",
        dataType:"json",
        data: {"action": "road_main_by_s_id", "series_s_id": series_s_id},
        async:false
    })
    .done(function(main_data_array){
        console.log('OP code: road_main_by_s_id, AJAX return:' + main_data_array);
        console.log('main_data_array.length = ' + main_data_array.length);
        
        $("#body_cenetr").empty(); // clear previous data on screen
        
        for(var i = 0; i < main_data_array.length; i++)
		{
            /*
			console.log("main_m_id: " + main_data_array[i]['m_id']);
            console.log("main_name: " + main_data_array[i]['name']);
            console.log("main_price: " + main_data_array[i]['price']);
            console.log("main_s_id: " + main_data_array[i]['s_id']);
            console.log("main_at_id: " + main_data_array[i]['at_id']);
            console.log("main_required_option: " + main_data_array[i]['required_option']);
            console.log("main_order_num: " + main_data_array[i]['order_num']);
            console.log('--------我是分隔線--------');
            */
            
            $("#body_cenetr").append(
                $("<div>").attr({'class': 'main', 'id': 'main_m_id_' + main_data_array[i]['m_id']})
                    .append($("<div>").attr({'class': 'main_name'})
                        .append(main_data_array[i]['name']))
                    .append($("<div>").attr({'class': 'main_price'})
                        .append(main_data_array[i]['price'] + '元'))
                    .data('main', {
                        'm_id': main_data_array[i]['m_id'],
                        'name': main_data_array[i]['name'],
                        'price': main_data_array[i]['price'],
                        's_id': main_data_array[i]['s_id'],
                        'at_id': main_data_array[i]['at_id'],
                        'required_option': main_data_array[i]['required_option'],
                        'order_num': main_data_array[i]['order_num']
                    })
            );
            
            console.log("main_m_id: " + $("#" + 'main_m_id_' + main_data_array[i]['m_id']).data('main').m_id);
            console.log("main_name: " + $("#" + 'main_m_id_' + main_data_array[i]['m_id']).data('main').name);
            console.log("main_price: " + $("#" + 'main_m_id_' + main_data_array[i]['m_id']).data('main').price);
            console.log("main_s_id: " + $("#" + 'main_m_id_' + main_data_array[i]['m_id']).data('main').s_id);
            console.log("main_at_id: " + $("#" + 'main_m_id_' + main_data_array[i]['m_id']).data('main').at_id);
            console.log("main_required_option: " + $("#" + 'main_m_id_' + main_data_array[i]['m_id']).data('main').required_option);
            console.log("main_order_num: " + $("#" + 'main_m_id_' + main_data_array[i]['m_id']).data('main').order_num);
            console.log('--------我是分隔線--------');
		}
        
        $("#body_cenetr").append( // 新增主餐
            $("<div>").attr({'class': 'main', 'id': 'add_main'}).append(
                '+新增'
            )
            .bind('click', add_main)
        );
    })
    .fail(function(){
        alertify.error('錯誤：無法取得主餐資料');
        console.log('AJAX error: road_main_by_s_id');
    })
    ;
    
    $('#body_cenetr').show();
    
    //console.log("Exit: road_main_by_s_id");
}

function open_edit_addition_windows(){
    $('#edit_addition_window').css({"display":"block"});
}

function road_add_type(){
    // 取得所有附加選項名稱
    $.ajax({
        url:"edit_menu_op.php",
        method: "POST",
        dataType:"json",
        data: {"action": "get_add_type"},
        async:false
	})
    .done(function(add_type_array){
        $('#single_choice_by_user').empty();
        $('#single_choice_by_user').append($('<div>').append('單選項目'));
        $('#single_choice_by_user').append($('<div>').attr({'class': 'edit_add_type', 'id': 'button_edit_single'}).append('編輯'));
        button_edit_addition_type('button_edit_single');
        $('#single_choice_by_user').append($('<br>'));
        $('#single_choice_by_user').append($('<hr>').attr({'width': '100%'}));
        
        $('#multi_choice_by_user').empty();
        $('#multi_choice_by_user').append($('<div>').append('多選附加項目'));
        $('#multi_choice_by_user').append($('<div>').attr({'class': 'edit_add_type', 'id': 'button_edit_multi'}).append('編輯'));
        button_edit_addition_type('button_edit_multi');
        $('#multi_choice_by_user').append($('<br>'));
        $('#multi_choice_by_user').append($('<hr>').attr({'width': '100%'}));
        $('#multi_choice_by_user') // 增加「無加點」選項
                .append($('<label>').attr({'for':'for_at_0'})
                    .append($('<div>').attr({'class': 'detail_item', 'id': 'add_type_at_id_0'})
                        .append($("<input>").attr({'type':'radio', 'name':'multi', 'value': '0', 'id':'for_at_0', 'checked':true}))
                        .append('無加點')
                        .data('add_type', {
                            'at_id': 0,
                            'option_name':'無加點',
                            'multiple_choice': 0}
                        )
                    )
                );

        $('#addition_left').empty();
        $('#addition_right').empty();

        for(var i = 0; i < add_type_array.length; i++){
            var add_type_at_id = add_type_array[i]['at_id'];
            var add_type_option_name = add_type_array[i]['option_name'];
            var add_type_multiple_choice = add_type_array[i]['multiple_choice'];
            
            /*
            console.log("add_type_at_id: " + add_type_array[i]['at_id']);
            console.log("add_type_option_name: " + add_type_array[i]['option_name']);
            console.log("add_type_multiple_choice: " + add_type_array[i]['multiple_choice']);
            console.log('--------我是分隔線--------');
            */
            
            var append_target;
            var input_type;
            var input_name;
            
            if(add_type_multiple_choice == '0'){ // 下單時為單選的細項
                append_target = '#single_choice_by_user';
                input_type = 'checkbox';
                input_name = 'single';
            }
            else{ // 下單時為多選的細項
                append_target = '#multi_choice_by_user';
                input_type = 'radio';
                input_name = 'multi';
            }

            $(append_target)
                .append($('<label>').attr({'for':'for_at_' + add_type_at_id})
                    .append($('<div>').attr({'class': 'detail_item', 'id': 'add_type_at_id_' + add_type_at_id})
                        .append($("<input>").attr({'type':input_type, 'name':input_name, 'value': add_type_at_id, 'id':'for_at_' + add_type_at_id}))
                        .append(add_type_option_name)
                        .data('add_type', {
                            'at_id':add_type_at_id,
                            'option_name':add_type_option_name,
                            'multiple_choice': add_type_multiple_choice}
                        )
                    )
                );

            $('#addition_left')
                .append($('<div>').attr({'class':'addition_type', 'id':'edit_add_type_at_id_' + add_type_at_id})
                    .append(add_type_option_name)
                    .data('add_type', {
                            'at_id':add_type_at_id,
                            'option_name':add_type_option_name,
                            'multiple_choice': add_type_multiple_choice}
                        )
                );

            console.log("add_type_at_id: " + $("#" + 'add_type_at_id_' + add_type_at_id).data('add_type').at_id);
            console.log("add_type_option_name: " + $("#" + 'add_type_at_id_' + add_type_at_id).data('add_type').option_name);
            console.log("add_type_multiple_choice: " + $("#" + 'add_type_at_id_' + add_type_at_id).data('add_type').multiple_choice);
            console.log('--------我是分隔線--------');
        }

        // 顯示選項「無加點」內之資料
        console.log("add_type_at_id: " + $("#" + 'add_type_at_id_0').data('add_type').at_id);
        console.log("add_type_option_name: " + $("#" + 'add_type_at_id_0').data('add_type').option_name);
        console.log("add_type_multiple_choice: " + $("#" + 'add_type_at_id_0').data('add_type').multiple_choice);
        console.log('--------我是分隔線--------');

        // 增加單選新增按鈕
        var edit_add_type_at_id_add_single = $('<div>').attr({'class':'addition_type', 'id':'edit_add_type_at_id_add_single'})
            .append('+新增')
            .data('add_type', {
                    'multiple_choice': 0}
                )
        edit_add_type_at_id_add_single.bind('click', {choice_type:'single_choice_by_user'}, add_additional_type); // .bind(event, data, function)
        $('#addition_left').append(edit_add_type_at_id_add_single);

        // 增加多選新增按鈕
        var edit_add_type_at_id_add_multi = $('<div>').attr({'class':'addition_type', 'id':'edit_add_type_at_id_add_multi'})
            .append('+新增')
            .data('add_type', {
                    'multiple_choice': 1}
            )
        edit_add_type_at_id_add_multi.bind('click', {choice_type:'multi_choice_by_user'}, add_additional_type); 
        $('#addition_left').append(edit_add_type_at_id_add_multi);

    })
    .fail(function(){
		alertify.error('錯誤：無法取得附加選項資料');
        console.log('AJAX error: get_add_type');
	});
}

function button_submit(target){
    var button_submit = $("#button_submit");
    var single_checked_value;
    var single_checked_array;
    var multi_checked_value;
    var main_name;
    var main_price;
    var error_msg;
    
    if(target == 'add'){
        button_submit.bind('click', function(){
            console.log('button_submit_for_add');

            single_checked_value = $("[name='single']:checked"); // 所有checked元素的陣列
            console.log('single_checked_value.size(): ' + single_checked_value.size());
            single_checked_array = Array();
            for(var i = 0; i < single_checked_value.size(); i++){
                console.log('Checked at_id: ' + $(single_checked_value[i]).val());
                single_checked_array.push($(single_checked_value[i]).val());
            }
            if(single_checked_value.size() == 0){
                single_checked_array.push(0);
            }
            
            multi_checked_value = $("[name='multi']:checked").val();
            console.log('multi_checked_value: ' + multi_checked_value);
            
            main_name = $('#main_name').val();
            console.log('main_name: ' + main_name);
            
            main_price = $('#main_price').val();
            console.log('main_price: ' + main_price);
            
            console.log('g_click_series_s_id: ' + g_click_series_s_id);
            
            error_msg = Array();
            if(main_name == ''){
                error_msg.push('請輸入餐點名稱！');
            }
            
            if(main_price == ''){
                error_msg.push('請輸入有效餐點金額！');
            }
            
            if(error_msg.length == 0){ // no error can submit
                
                $.ajax({
					url:"edit_menu_op.php",
					method: "POST",
					dataType:"json",
                    data: {
                        "action": "add_item", 
                        "single_at_id": single_checked_array, 
                        "mul_at_id": multi_checked_value, 
                        "new_main_series": g_click_series_s_id, 
                        "new_main_name": main_name, 
                        "new_main_price": main_price
                    },
                    async:false
                })
                .done(function(){
                    alertify.success("新增主餐成功！");
                    //window.location.reload();
                    $('#series_s_id_' + g_click_series_s_id).trigger('click');
                    $("#button_cancel").trigger('click');
                })
                .fail(function(){
                    alertify.error("新增主餐傳輸錯誤！");
                });
                
            }
            else{
                alertify.error(error_msg.join("<br>"));
            }
            
        });      
    }
    else if(target == 'edit'){
        button_submit.bind('click', function(){
            console.log('button_submit_for_edit');
        });      
    }
    else{
        alert('Unknown target for button_submit()');
    }
}

function button_edit_addition_type(target){
    var button_edit_addition_type = $('#' + target);
    var hide_target;

    button_edit_addition_type.bind('click', function(){

        console.log('Click: ' + target);

        var edit_add_type_at_id_array = $("div[id^='edit_add_type_at_id_']");
        console.log('edit_add_type_at_id_array.size() = ' + edit_add_type_at_id_array.size());

        $('#edit_addition_window_title').empty();
        $('#edit_addition_window_text').empty();
        if(target == 'button_edit_single'){
            $('#edit_addition_window_title').append('編輯 單選選項');
            $('#edit_addition_window_text').append('點餐時只能單選的項目');
            hide_target = 1;
        }
        else{
            $('#edit_addition_window_title').append('編輯 多選選項');
            $('#edit_addition_window_text').append('點餐時可以多選的項目');
            hide_target = 0;
        }

        for(var i = 0; i < edit_add_type_at_id_array.size(); i++){
            //console.log($(edit_add_type_at_id_array[i]).data('add_type').at_id);
            if($(edit_add_type_at_id_array[i]).data('add_type').multiple_choice == hide_target){
                $(edit_add_type_at_id_array[i]).hide();
            }
            else{
                $(edit_add_type_at_id_array[i]).show();
            }
        }

        open_edit_addition_windows();
    });
}

function button_cancel(){
    var button_cancel = $("#button_cancel");
    button_cancel.bind('click', function(){
        
        $('#body_right').hide();
        road_add_type();
        $('#main_name').val('');
        $('#main_price').val(0);
    });
}

function button_cancel(){
    var button_cancel = $("#button_cancel");
    button_cancel.bind('click', function(){

        $('#body_right').hide();
        road_add_type();
        $('#main_name').val('');
        $('#main_price').val(0);
    });
}

function button_close(){
    var button_close = $("#button_close");
    button_close.bind('click', function(){
        close_edit_addition_windows();
    });
}

function button_finish(){
    var button_finish = $("#button_finish");
    button_finish.bind('click', function(){
        close_edit_addition_windows();
    });
}

function add_series() {
	var new_series;
	alertify.prompt("請輸入新系列名稱", function(e, new_series) {
		if(e && (new_series != "")) {
			$.ajax({
				url:"edit_menu_op.php",
				method: "POST",
				dataType:"json",
				data: {"action": "add_series", "new_series": new_series, "order_number": 1},
				async:false
			})
			.done(function(msg){
				//console.log('OP code: add_series, AJAX return value: ' + msg);
				alertify.success("新增系列：" + new_series);
				window.location.reload();
			})
		    .fail(function(){
				alertify.error('新增系列失敗！');
			})
			;
		}
		else {
			alertify.error('新增系列操作取消！');
		} 
	}, "");
}

function add_additional_type(event){
    var new_type_name;
    var title;
    var multi_choice;
    var trigger_target;
    var success_msg;
    if(event.data.choice_type == 'single_choice_by_user'){ // 下單時之單選項目
        console.log("新增使用者單選類別");
        title = '請輸入新單選類別名稱';
        multi_choice = '0';
        trigger_target = '#button_edit_single';
        success_msg = '新單選類別：';
    }
    else if(event.data.choice_type == 'multi_choice_by_user'){ // 下單時之多選項目
        console.log("新增使用者多選類別");
        title = '請輸入新多選類別名稱';
        multi_choice = '1';
        trigger_target = '#button_edit_multi';
        success_msg = '新多選類別：';
    }
    else{
        alertify.error('錯誤：未知加點類別！');
        return;
    }

    alertify.prompt(title, function(e, new_type_name) {
        if(e && (new_type_name != "")) {
            $.ajax({
                url:"edit_menu_op.php",
                method: "POST",
                dataType:"json",
                data: {
                    "action": "add_additional_type",
                    "option_name":new_type_name,
                    "multiple_choice":multi_choice
                },
				async:false
			})
			.done(function(){
                road_add_type();
                $(trigger_target).trigger('click');
                alertify.success(success_msg + new_type_name);
			})
			.fail(function(){
                alertify.error("新增品項傳輸錯誤！");
			});
		}
		else {
            alertify.error('新增多選細項類別取消！');
		}
	}, "");
}

function add_main(){
    console.log('Enter: add_main()');
    g_click_main_m_id = 0;
    $('#button_delete').hide();
    
    if(g_button_submit_bind_state == true){
        $("#button_submit").unbind();
        g_button_submit_bind_state = false;
    }
    
    if(g_button_cancel_bind_state == true){
        $("#button_cancel").unbind();
        g_button_cancel_bind_state = false;
    }
    
    if(g_button_submit_bind_state == false){
        button_submit('add');
        g_button_submit_bind_state = true;
    }
    
    if(g_button_cancel_bind_state == false){
        button_cancel();
        g_button_cancel_bind_state = true;
    }
    
    $('#body_right').show();
    
    console.log('Exit: add_main()');
}

function close_edit_addition_windows(){
    //console.log('close_edit_addition_windows');
    $('#edit_addition_window').css({"display":"none"});
}

$(document).ready(function(){
    $("div[id^='series_s_id_']").bind('click', road_main_by_s_id);
    
    road_add_type();
    
    $("#add_series").bind("click", add_series);// 新增系列
    
    button_close();
    button_finish();

    // navbar_setting
    $("#nav_cross").click(function(){
        $(".w3-sidenav").toggle();
        $("#Main").css({"marginLeft":"0%"});
        $(".w3-sidenav").css({"display":"none"});
        $(".w3-opennav").css({"display":"inline-block"});
    });

    $("#nav_open").click(function(){                            
        $(".w3-sidenav").toggle();
        $("#Main").css({"marginLeft":"20%"});
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
});
