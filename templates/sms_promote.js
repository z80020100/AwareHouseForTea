$(document).ready(function(){
    $("#promote_table").tablesorter();
    $("#promote_table").tablesorter({ sortList: [[0,0], [1,0]] });
    
    // send sms button
    $("#sms_button").click(function(){
       $("#sms_content").css({"display":"block"});
    });
    $("#close_sms_content").click(function(){
       $("#sms_content").css({"display":"none"});
    });

    // sms_mode selector tab
    $(".tab").on("click", function() {
        $(this).addClass('active');
        $(this).siblings('div.tab').removeClass('active');
        $('#num_results').text('0');
        $('#promote_table').empty().append('<tbody><tr><td colspan="5">請點選搜尋</td></tr></tbody>');
    });

    // query results
    $("#search_button").on("click", function() {
        var sms_mode = $('.tab.active').attr('sms-mode');
        var starttime = new Date( $('input[name=filter1]').val() ).getTime() / 1000;
        var endtime = new Date( $('input[name=filter2]').val() ).getTime() / 1000;
        var baseline = $('select[name=filter3]').val();

        console.log('sms_mode = ' + sms_mode);
        console.log('starttime = ' + starttime);
        console.log('endtime = ' + endtime);
        console.log('baseline = ' + baseline);

        if (!starttime || !endtime || !baseline) {
            alert('請記得 設定篩選器 or 使用正確的篩選值！');
            return;
        }

        $('#filter_starttime').text( $('input[name=filter1]').val() );
        $('#filter_endtime').text( $('input[name=filter2]').val() );
        $('#filter_dollar').text( 'NT$' + $('select[name=filter3]').val() + ' 以上' );

        $.ajax({
            url:"sms_promote.php",
            method: "POST",
            dataType: "json",
            data: {
                "sms_mode": sms_mode,
                "starttime": starttime,
                "endtime": endtime,
                "baseline": baseline
            },
            async: false
        })
        .done(function(msg) {
            handle_query(msg);
            $('#num_results').text(msg.length);
        })
        .fail(function() {
            alert('query failed!');
        });
    });
});

function add_customer_row(phone_number, last_order, order_num, amount) {
    $('table tbody').append(
        $('<tr>').append(
            $('<td>').html("<input class=\"w3-check sms_checkbox\" type=\"checkbox\">"),
            $('<td>').text(phone_number),
            $('<td>').text(last_order),
            $('<td>').text(order_num),
            $('<td>').text(amount)
        )
    );
    return;
}

function add_shop_row(phonenumber, shopname, shopowner) {
    $('table tbody').append(
        $('<tr>').append(
            $('<td>').html("<input class=\"w3-check sms_checkbox\" type=\"checkbox\">"),
            $('<td>').text(phonenumber),
            $('<td>').text(shopname),
            $('<td>').text(shopowner)
        )
    );
    return;
}

function handle_query(msg) {
    
    // sms_mode is depended on selected tab
    // sms_mode, 0 for customer, 1 for shop
    var sms_mode = $('.tab.active').attr('sms-mode');

    if (sms_mode == 0) {
        // add thead content
        $('table').empty().append(
            $('<thead>').append(
                $('<tr>').append(
                    $('<td rowspan="2">').append(
                        '全選 <input class="w3-check sms_checkbox" type="checkbox" id="select_all">'
                    ),
                    $('<td rowspan="2">').text('號碼'),
                    $('<td colspan="3">').text('統計報表')
                ),
                $('<tr>').append(
                    $('<td>').text('最後一次消費時間'),
                    $('<td>').text('期間消費次數'),
                    $('<td>').text('累計消費金額')
                )
            ),
            $('<tbody>')
        );
        // add results of query into tbody
        for (var i = 0; i < msg.length; i++) {
            add_customer_row(msg[i]['ui_phone'], 0, msg[i]['times'], msg[i]['price']);
        }
    }
    else if (sms_mode == 1) {
        // add thead content
        $('table').empty().append(
            $('<thead>').append(
                $('<tr>').append(
                    $('<td>').append(
                        '全選 <input class="w3-check sms_checkbox" type="checkbox" id="select_all">'
                    ),
                    $('<td>').text('老闆電話'),
                    $('<td>').text('分店名稱'),
                    $('<td>').text('分店老闆')
                )
            ),
            $('<tbody>')
        );
        // add results of query into tbody
        for (var i = 0; i < msg.length; i++) {
            add_shop_row(msg[i]['ui_phone'], msg[i]['shop_name'], msg[i]['shop_owner']);
        }
    }

    // after results put into tbody, add select_all event
    $("#select_all").on("click", function() {
        var checkstatus = this.checked;
        $('.sms_checkbox').each(function () {
            this.checked = checkstatus;
        });
    });
}
