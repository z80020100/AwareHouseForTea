$(document).ready(function(){
    $("#promote_table").tablesorter();
    $("#promote_table").tablesorter({ sortList: [[0,0], [1,0]] });
    add_table_row("0912123123","2016/07/04","11","10000","abcedd","12");

    $("#sms_button").click(function(){
       $("#sms_content").css({"display":"block"});
    });
    $("#close_sms_content").click(function(){
       $("#sms_content").css({"display":"none"});
    });

    $("#select_all").on("click", function() {
        if(this.checked){
            $('.sms_checkbox').each(function () {
                this.checked = true;
            });
        }
        else{
            $('.sms_checkbox').each(function () {
                this.checked = false;
            });
            
        }
    });
});

function add_table_row(phone_number, order_time, order_num, amount, content, sms_num){
    $('table tbody').append(
        $('<tr>').append(
            $('<td>').html("<input class=\"w3-check sms_checkbox\" type=\"checkbox\">"),
            $('<td>').text(phone_number),
            $('<td>').text(order_time),
            $('<td>').text(order_num),
            $('<td>').text(amount),
            $('<td>').text(content),
            $('<td>').text(sms_num)
        )
    );
    return;
}
