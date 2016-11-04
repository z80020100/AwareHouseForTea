$(document).ready(function(){
    $("#promote_table").tablesorter();
    $("#promote_table").tablesorter({ sortList: [[0,0], [1,0]] });
    // add_customer_row("0912123123","11","10000","12");

    $("#sms_button").click(function(){
       $("#sms_content").css({"display":"block"});
    });
    $("#close_sms_content").click(function(){
       $("#sms_content").css({"display":"none"});
    });

    // mode selector
    $(".tab").on("click", function() {
        $(this).addClass('active');
        $(this).siblings('div.tab').removeClass('active');
        $('#promote_table').empty().append('<tbody><tr><td colspan="5">請點選搜尋</td></tr></tbody>');
    });

    // search results
    $("#search_button").on("click", function() {
        $.ajax({
            url:"sms_promote.php",
            method: "POST",
            dataType: "json",
            data: {"sms_mode": $('.tab.active').attr('sms-mode')}, // sms_mode, 0 for customer, 1 for shop
            async: false
        })
        .done(function(msg) {
            handle_query(msg);
        })
        .fail(function() {
            alert('query failed!');
        });
    });
});

function add_customer_row(phone_number, order_num, amount, sms_num) {
    $('table tbody').append(
        $('<tr>').append(
            $('<td>').html("<input class=\"w3-check sms_checkbox\" type=\"checkbox\">"),
            $('<td>').text(phone_number),
            $('<td>').text(order_num),
            $('<td>').text(amount),
            $('<td>').text(sms_num)
        )
    );
    return;
}

function add_shop_row(shopid, shopname, shopowner) {
    $('table tbody').append(
        $('<tr>').append(
            $('<td>').html("<input class=\"w3-check sms_checkbox\" type=\"checkbox\">"),
            $('<td>').text(shopid),
            $('<td>').text(shopname),
            $('<td>').text(shopowner)
        )
    );
    return;
}

function handle_query(msg) {
    
    // sms_mode, 0 for customer, 1 for shop
    var sms_mode = $('.tab.active').attr('sms-mode');

    if (sms_mode == 0) {
        $('table').empty().append(
            $('<thead>').append(
                $('<tr>').append(
                    $('<td rowspan="2">').append(
                        '全選 <input class="w3-check sms_checkbox" type="checkbox" id="select_all">'
                    ),
                    $('<td rowspan="2">').text('號碼'),
                    $('<td colspan="2">').text('統計報表'),
                    $('<td rowspan="2">').text('已推播簡訊')
                ),
                $('<tr>').append(
                    $('<td>').text('消費次數'),
                    $('<td>').text('累計消費金額')
                )
            ),
            $('<tbody>')
        );
        for (var i = 0; i < msg.length; i++) {
            add_customer_row(msg[i]['ui_phone'], msg[i]['times'], msg[i]['price'],0);
        }
    }
    else if (sms_mode == 1) {
        $('table').empty().append(
            $('<thead>').append(
                $('<tr>').append(
                    $('<td>').append(
                        '全選 <input class="w3-check sms_checkbox" type="checkbox" id="select_all">'
                    ),
                    $('<td>').text('分店 ID'),
                    $('<td>').text('分店名稱'),
                    $('<td>').text('分店老闆')
                )
            ),
            $('<tbody>')
        );
        for (var i = 0; i < msg.length; i++) {
            add_shop_row(msg[i]['shop_id'], msg[i]['shop_name'], msg[i]['shop_owner']);
        }
    }

    $("#select_all").on("click", function() {
        var checkstatus = this.checked;
        $('.sms_checkbox').each(function () {
            this.checked = checkstatus;
        });
    });
}
