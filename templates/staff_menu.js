
var order_info=new Object();
order_info["share_array"]=[];
order_info["table_num"]="16";
order_info["people_num"]="2";


order_info["share_array"][0]=new Object();
order_info["share_array"][0]["items_array"]=[];
var cacheItems = new Array(3);

for (var l=0; l<cacheItems.length; ++l){
    cacheItems[l] = new Array();
}


var cacheIndex = 0;
var cacheIndex_left = cacheItems.length-1;
var cacheIndex_right = 1;
var items_array_length=0;
var amount_result = 1;
var tprice_ori = 0;
var first_cal_button = false;
var total_amount = 0;
var getFreeFlag = true;

$(document).ready(function(){
        getFreeFlag = true;
        $("#now").text(cacheIndex);

        $(".calculator").hide();

        $("#nav_open").parent().remove()
        //$("#nav_open").remove()

        $("#open_cart").click(function(){
            $("#cart_list").css({"display":"block"});
            $("button[id^='m']").attr('disabled', 'disabled');
        });

        $("#close_cart").click(function(){
            $("#cart_list").css({"display":"none"});
            $("button[id^='m']").removeAttr('disabled');
        });

        $("button[id^='m']").click(function(){
            //$("button[id^='m']").attr('disabled', 'disabled');
            //$("#materialblock").empty();

            var at_id_array = [];
            at_id_array[0] = $(this).attr("data-at_id");

            for(var i =0 ; i< $(this).data("ro_at_id").length-1; ++i){
                at_id_array[i+1] = $(this).data("ro_at_id")[i];
            }

            //load_ajax(at_id_array);

            $("#item_name").html($(this)[0].innerHTML);
            $("#item_price").val(parseInt($(this)[0].value) );
            $("#m_price").val(parseInt($(this)[0].value));
            $("#m_id").val(($(this)[0].id).slice(1));
            if(amount_result != 0)
                add_row($("#item_name").html(),amount_result, $("#item_price").val());
            cal_price();
            hide_calculator();
        });

        $("#close_confirm").click(function(){
            // reset all the value
            $("button[id^='m']").removeAttr('disabled');
            amount_result = 1;
            document.getElementById('amountResult').innerHTML = 1;
            //$("#amountButton").val("1");
            $("#materialblock").empty();
            $("#addition_info").val("");
            $("#confirm_item").css({"display":"none"});
            first_cal_button = false;
        });

        $("#add_cart").click(function(){
            if(document.getElementById('amountResult').innerHTML ==0){
                alertify.success("數量不能為0");
                return;
            }
            add_row($("#item_name").html(),amount_result, $("#item_price").val());

            $("#add_success").css({"position":"fixed", "width":"100%", "height":"0px", "top":"0px", "left":"0px"});
            $("#add_success").css("visibility","visible");
            $("#add_success").animate({height:"30px"});

            var interval_id = setInterval(function () {
               $("#add_success").css("visibility","hidden");
               $("#add_success").css({"position":"fixed", "width":"100%", "height":"0px"});
               clearInterval(interval_id);
            },2000);

            $("button[id^='m']").removeAttr('disabled');
            //alert("成功加入購物車");
            $("#close_confirm").click();

        });

        $("#send_order").click(function(){
            if($("#orderList").find("tr").length <= 1){
                alertify.success("你尚未點選任何品項");
                return;
            }

            if($("#table_num").val()== ""){
                alertify.success("你尚未填寫桌號");
                return;
            }

            if($("#people_num").val()==""){
                alertify.success("你尚未填寫人數");
                return;
            }

            $("#orderList").find("tr").each(function(index, value){
                    order_info["share_array"][0]["items_array"].push($(this).data("item_array"));
            });
      	    order_info["table_num"]=$("#table_num").val();
      	    order_info["people_num"]=$("#people_num").val();

            $("#check_out_price").val($("#tprice").val());
            document.getElementById('check_out_amountResult').innerHTML = 0;
            $("#the_change").val(0);
            $("#check_out").css({"display":"block"});
            first_cal_button = false;
            total_amount = 0;
        });

        $("#check_out_button").click(function(){
          $("#check_out_button").attr('disabled', 'disabled');
          send_total_order();


          // reset all the value
          $("button[id^='m']").removeAttr('disabled');
          amount_result = 1;
          document.getElementById('amountResult').innerHTML = 1;
          //$("#amountButton").val("1");
          //$("#materialblock").empty();
          $("#addition_info").val("");
          $("#confirm_item").css({"display":"none"});
          $("#check_out").css({"display":"none"});
          first_cal_button = false;
        });

        $("#check_out_close_confirm").click(function(){
            // reset all the value
            $("button[id^='m']").removeAttr('disabled');
            amount_result = 1;
            document.getElementById('amountResult').innerHTML = 1;
            //$("#amountButton").val("1");
            //$("#materialblock").empty();
            $("#addition_info").val("");
            $("#confirm_item").css({"display":"none"});
            $("#check_out").css({"display":"none"});
            first_cal_button = false;
        });

        $('.cal_button').unbind('click').click(function(){
          if(!first_cal_button){
            first_cal_button = true;
            amount_result = 0;
          }
          switch(this.value){
            //console.log(this.value());
            case "C":
                amount_result = 0;
                break;
            case "-1":
                amount_result = parseInt(amount_result/10,10);
                break;
            default:
                if(amount_result == 0)
                  amount_result = parseInt(this.value);
                else
                  amount_result = amount_result*10 + parseInt(this.value);
          }
        //   console.log(amount_result);
          document.getElementById('amountResult').innerHTML = amount_result;
          document.getElementById('check_out_amountResult').innerHTML = amount_result;
          $("#the_change").val(amount_result-$("#check_out_price").val());
          cal_price();

        })
        //hidden menu expect first menu
        for (i = 1; i < $(".menu").length; i++) {
           $(".menu")[i].style.display = "none";
        }
        $("#cart_list").css({"display":"block"});

    });

function openMenu(menuName) {
  var i;
  for (i = 0; i < $(".menu").length; i++) {
     $(".menu")[i].style.display = "none";
  }
  document.getElementById(menuName).style.display = "block";
}

function cal_price(){

    var total_price = $("#m_price").val();

    $("#materialblock").find("input:checked").each(function(index, value){
        total_price = parseInt(total_price) + parseInt($(this).val());
    });

    $("#item_price").val(parseInt(total_price) * parseInt(amount_result) );
    // console.log("amount_result"+amount_result);
}

function discount( dis){
  if(tprice_ori > 0)
    $("#tprice").val( parseInt(tprice_ori * dis /10));
}

function hide_calculator(){
    $(".calculator").hide();
    $("#table_cart").css("height","70vh");
    amount_result=1;
    $("#amountResult").text("1");
    first_cal_button=false;
}
function show_calculator(){
    if( $('.calculator').is(':visible') ){
        hide_calculator();
    } else {
        $(".calculator").show();
        $("#table_cart").css("height","28vh");
    }
}

function get_free(){
    var free_number = parseInt(total_amount / 6);
    var price;
    var amount;
    $("#tprice").val(0);
    $("#orderList").find("tr").each(function(index, value){
        if(index > 0){
            amount = $(this).find("td").eq(1).text();
            price = $(this).find("td").eq(2).text();
            if(parseInt(price) < 0){
                $(this).remove();
            }else {
                $("#tprice").val( parseInt($("#tprice").val()) + parseInt(price) * amount );
            }
        }
    });
    if(getFreeFlag == false)
        return;

    var arr = [];
    $('#orderList tr').has('td').each(function() {
        var arrayItem = {};
        var i=0;
        $('td', $(this)).each(function(index, item) {
            arrayItem[index] = $(item).html();
        });
        for(i=0; i<arrayItem[1]; i++)
            arr.push(arrayItem);
    });

    arr.sort(function(a,b){
        return parseInt($(b)[0][2]) - parseInt($(a)[0][2]);
    });
    $("#orderList").find("tr").each(function(index, value){
        $(this).removeClass('free');
    });

    var name;
    var price;
    var comment;
    for (var i=0; i<free_number ; ++i){
        name = arr[arr.length-1-i][0];
        price = arr[arr.length-1-i][2];
        comment = arr[arr.length-1-i][3];
        add_row(name, -1, -price, comment);
        $("#tprice").val( parseInt($("#tprice").val()) - parseInt(price) );
    }
}

function add_row( name, amount, price){
    add_row( name, amount, price, "");
}
function add_row( name, amount, price, custom_comment){
    var item_array= new Object();
    item_array["m_id"] = $("#m_id").val();
    item_array["quantity"] = amount;
    item_array["comment"] = "";
    item_array["RO_array"] = [];
    item_array["AI_array"] = [];

    var RO_array_length=0;
    var AI_array_length=0;
    // var custom_comment="";
    //
    // custom_comment.concat($("#addition_info").val());
    // $("#materialblock").find("input:checked").each(function(index, value){
    //     custom_comment = custom_comment.concat($(this).data("name")+" ");
    //
    //     if($(this).data("is_ro")==1){
    //         item_array["RO_array"][RO_array_length]=$(this).data("ai_id");
    //         ++RO_array_length;
    //     }
    //     else{
    //         item_array["AI_array"][AI_array_length]=$(this).data("ai_id");
    //         ++AI_array_length;
    //     }
    // });
    // custom_comment = custom_comment + "";

    if(amount > 0) {
        total_amount += amount;
    }
    //if(total_amount > 5)

    // if(merge_order_list(name, amount, price, custom_comment)==false){
    //     //alert("merge");
    //     return;
    // }


    var tr_temp = $('<tr>');
    if(amount < 0)
        tr_temp.addClass('free');
   //  order_info["share_array"][0]["items_array"].push(item_array);
   //  ++items_array_length;
   //  alert(order_info["share_array"]["items_array"][0]);
   // alert(items_array_length);
   // alert(item_array["m_id"]);
   //  alert(order_info["share_array"]["items_array"][0]["m_id"]);
    $('table tbody').append(
         tr_temp.append(
                $('<td>').text(name),
                $('<td>').text(amount),
                $('<td>').text(price),
                $('<td>').text(custom_comment)
         )
    );


    if(parseInt(price) > 0){
        tr_temp.click(function(event) {
            $(this).toggleClass('selected');
        });
        // Swipe Left
        tr_temp.on("swipeleft",function(){
            $("#tprice").val( parseInt(tprice_ori) );
            price = parseInt($(this).find("td").eq(2).text());
            amount = parseInt($(this).find("td").eq(1).text())
            $("#tprice").val( parseInt($("#tprice").val()) -$(this).find("td").eq(2).text());
            tprice_ori = $("#tprice").val();
            total_amount = total_amount - amount ;
            $(this).remove();
            if(amount > 0 )
                get_free();
        });
    }
    // Swipe Right
    tr_temp.on("swiperight",function(){
            $("#tprice").val( parseInt($("#tprice").val()) -$(this).find("td").eq(2).text());
            $(this).find("td").eq(2).text("0");
            $(this).addClass('free');
            getFreeFlag = false;
            get_free();
    });

    tr_temp.data("item_array", item_array);
    tr_temp.data("m_price", $("#m_price").val());

    //$("#tprice").val( parseInt(tprice_ori) );
    //$("#tprice").val( parseInt($("#tprice").val()) + parseInt($("#item_price").val())*amount );
    //tprice_ori = $("#tprice").val();

    if(amount > 0)
        get_free();
}

function remove_row(){
    $('table tr:last').remove();
}

function load_ajax(at_id_array){
   var at_id = new Object();
   at_id["at_id_array"] = at_id_array;

    $.ajax( {
        url:"get_ai.php",
        method: "POST",
        dataType:"json",
        data: {"at_id":at_id}
    } )
    .done(function(msg){
        console.log(msg);
       /* alert(msg);
        alert(msg[0]['multiple_choice']);
        alert(msg.length);*/

        for(var q=0; q<msg.length ; ++q){
            var input_length = $("#materialblock").find("input").length;
            //var htmldata=$("#materialblock").html();
            var htmldata="";

            htmldata = "<div class=\"muti_choice\">";
            if(msg[q]['multiple_choice'] == 1){
                for(var i =0; i<msg[q]['ais'].length ;++i){
                    htmldata =htmldata + "<input type=\"checkbox\" class=\"w3-check\" id=\"muti"+ q + "\_" + i;
                    htmldata =htmldata + "\" ><label class=\"w3-validate\" for=\"muti" + q + "\_" + i + "\">" +msg[q]['ais'][i].name+"</label>";
                }
                htmldata = htmldata + "</div>";
                $("#materialblock").append(htmldata);
                $("#materialblock").find("input").addClass("addmaterial");
               // $("#materialblock").find("input").css({"background-color":"#ef5285", "color":"#ff7473"});

                var upperbound =  parseInt(msg[q]['ais'].length)+parseInt(input_length);
                for(var i =parseInt(input_length); i<upperbound ;++i){
                    $("#materialblock").find("input").eq(i).data("name", msg[q]['ais'][i-parseInt(input_length)].name);
                    $("#materialblock").find("input").eq(i).data("ai_id", msg[q]['ais'][i-parseInt(input_length)].ai_id);
                    $("#materialblock").find("input").eq(i).data("is_ro", msg[q]['is_ro']);
                    $("#materialblock").find("input").eq(i).val(msg[q]['ais'][i-parseInt(input_length)].price);
                }
            }
            else{
                htmldata = "<div class=\"one_choice\">";
                for(var i = 0; i<msg[q]['ais'].length; ++i){
                    if(i==0){
                        htmldata = htmldata + "<input type=\"radio\" id=\"one"+ q + "\_" + i + "\" name=\"r"+q+ "\" checked >";
                        htmldata = htmldata + "<label class=\"w3-validate\" for=\"one" + q + "\_" + i + "\">" +msg[q]['ais'][i].name+"</label> ";
                    }
                    else{
                      htmldata = htmldata + "<input type=\"radio\" id=\"one"+ q + "\_" + i + "\" name=\"r"+q+ "\" >";
                      htmldata = htmldata + "<label class=\"w3-validate\" for=\"one" + q + "\_" + i + "\">" +msg[q]['ais'][i].name+"</label> ";                    }
                }
                htmldata = htmldata + "</div>";
                $("#materialblock").append(htmldata);
                $("#materialblock").find("input").addClass("addmaterial w3-radio");

                var upperbound =  parseInt(msg[q]['ais'].length)+parseInt(input_length);
                for(var i =parseInt(input_length); i<upperbound ;++i){
                    $("#materialblock").find("input").eq(i).val(msg[q]['ais'][i-parseInt(input_length)].price);
                    $("#materialblock").find("input").eq(i).data("name", msg[q]['ais'][i-parseInt(input_length)].name);
                    $("#materialblock").find("input").eq(i).data("ai_id", msg[q]['ais'][i-parseInt(input_length)].ai_id);
                    $("#materialblock").find("input").eq(i).data("is_ro", msg[q]['is_ro']);
                }
            }
        }

        $("#materialblock").find("input").on("click", function(){
            cal_price();
        });



        //$("button[id^='m']").attr('disabled', 'disabled');

    })
    .fail(function(){
        alert("fail1");
    })
    .always(function(){
        cal_price();
    //    $("#confirm_item").css({"display":"block"});
    //    alert("complete");
    })
    ;

}

function send_total_order(){
    $.ajax( {
        url:"order_response.php",
        method: "POST",
        dataType:"text",
        data: {"order_info":order_info, "req":"confirm_sum"}
    } )
    .done(function(msg){
        alertify.success("下單成功!");

     //   var value = document.getElementById("orderList").innerHTML;
     //   var printPage = window.open("", "", "width=200,height=100");
     //   printPage.document.open();

     //   printPage.resizeTo(0,0);
     //   printPage.moveTo(0,window.screen.availHeight+10);
     //   printPage.document.write("<HTML><head></head><BODY onload='window.print(); window.close();'><table id='orderList'></table>");
     //   printPage.document.write("<PRE>");
     //   printPage.document.getElementById('orderList').innerHTML = value;
     //   printPage.document.write("</PRE>");
     //   printPage.document.close("</BODY></HTML>");
    })
    .fail(function(){
        alert("fail2");
    })
    .always(function(){
        $("#orderList").find("tr").each(function(index, value){
                $(this).remove();
        });

        $("#tprice").val(0);
        getFreeFlag = true;
        //order_info["table_num"]=$("#table_num").val();
        //order_info["people_num"]=$("#people_num").val();
        order_info["share_array"][0]["items_array"]=[];
            //$("#cart_list").css({"display":"none"});
            //$("button[id^='m']").removeAttr('disabled');
            $("#send_order").removeAttr('disabled');
            $("#check_out_button").removeAttr('disabled');
    });
}

function cacheList(isRight){
    $("#tprice").val(0);
    cacheItems[cacheIndex].length = 0;
    $("#orderList").find("tr").each(function(index, value){
        if(index > 0)
            cacheItems[cacheIndex].push(value);
    });

    if(parseInt(isRight) == 1){
        cacheIndex_left = cacheIndex;
        cacheIndex = cacheIndex_right;
        if((cacheIndex_right+1) % cacheItems.length == 0){
            cacheIndex_right = 0;
        }
        else{
            cacheIndex_right ++;
        }
    }
    else{
        cacheIndex_right = cacheIndex;
        cacheIndex = cacheIndex_left;
        if((cacheIndex_left-1) < 0){
            cacheIndex_left = cacheItems.length -1;
        }
        else{
            cacheIndex_left --;
        }
    }

    $("#orderList").find("tr").each(function(index, value){
        if(index > 0)
            $(this).remove();
    });
    for (var s=0; s<cacheItems[cacheIndex].length ; ++s){
        $('table tbody').append(
            cacheItems[cacheIndex]
        );
    }
    total_amount = 0;
    $("#orderList").find("tr").each(function(index, value){
        var amount;
        if(index > 0) {
            $("#tprice").val( parseInt($("#tprice").val()) + parseInt($(this).find("td").eq(2).text()));
            amount = parseInt($(this).find("td").eq(1).text());
            if(amount > 0) {
                total_amount += amount;
                $(this).on("swipeleft",function(){
                    $("#tprice").val( parseInt(tprice_ori) );
                    $(this).remove();
                    $("#tprice").val( parseInt($("#tprice").val()) -$(this).find("td").eq(2).text());
                    tprice_ori = $("#tprice").val();
                    total_amount = total_amount - parseInt($(this).find("td").eq(1).text()) ;
                    get_free();
                });
                $(this).click(function(event) {
                    $(this).toggleClass('selected');
                });
            }
        }
    });
    $("#now").text(cacheIndex);
    // swip left
    // cal price

}

function pass2list(name,price){
    var haveSelected = false;
    $("#orderList").find("tr").each(function(index, value){
        if(index > 0 && $(this).hasClass("selected")){
            haveSelected = true;
        }
    });

    if(haveSelected == false){
        $('#orderList tbody tr:last').addClass("selected");
    }

    $("#orderList").find("tr").each(function(index, value){
        if(index > 0 && $(this).hasClass("selected")){
            var comment = $(this).find("td").eq(3);
            var new_price = $(this).find("td").eq(2);

            comment.text( comment.text() + ' ' + name);
            if($(this).hasClass("free"))
                new_price.text("0");
            else
                new_price.text( parseInt(new_price.text()) + parseInt(price));
            $(this).removeClass('selected');
        }
    });

    get_free();
}
function merge_order_list(name, amount, price, comment){
    var merge = true ;
    $("#orderList").find("tr").each(function(index, value){
          var temp_name = $(this).children("td").eq(0).text();
          var temp_amount = $(this).children("td").eq(1).text();
          var temp_price = $(this).children("td").eq(2).text();
          var temp_comment = $(this).children("td").eq(3).text();

          if((name==temp_name) && (comment==temp_comment)){
            cal_price();
            var single_list_price = parseInt(temp_price)/parseInt(temp_amount);
            $(this).children("td").eq(1).text(parseInt(temp_amount) +parseInt(amount));
            $(this).children("td").eq(2).text(parseInt(single_list_price) * parseInt($(this).children("td").eq(1).text()));

            $(this).data("item_array")["quantity"] = parseInt($(this).data("item_array")["quantity"]) + parseInt(amount);

            var sum=0;
            $("#orderList").find("tr").each(function(index, value){
                    sum = parseInt(sum) + parseInt($(this).children("td").eq(2).text());
            });
            $("#tprice").val(sum);
            tprice_ori = sum;
            merge = false;
            return merge;
          }
    });

    return merge;
}
