$(document).ready(function(){
    $.ajax({
        url:"sms_promote.php",
        method: "POST",
        dataType: "json",
        data: {
            "sms_mode": 1
        },
        async: false
    })
    .done(function(msg) {
        $('#num_results').text(msg.length);
        for (var i = 0; i < msg.length; i++) {
            console.log(msg[i]['shop_name']);
            console.log(msg[i]['shop_owner']);
            console.log(msg[i]['shop_address']);
            console.log(msg[i]['ui_phone']);
            console.log(msg[i]['shop_tel']);

    var shop_name = $($('<h2>').attr({'class': 'w3-padding'})).append(msg[i]['shop_name']);
    var shop_owner = $($('<h4>').attr({'class': 'w3-padding-left w3-margin-left'})).append($($('<li>')).append('加盟主姓名:'+ msg[i]['shop_owner']));
    var ui_phone = $($('<h4>').attr({'class': 'w3-padding-left w3-margin-left'})).append($($('<li>')).append('加盟主電話:' + msg[i]['ui_phone']));
    var shop_tel = $($('<h4>').attr({'class': 'w3-padding-left w3-margin-left'})).append($($('<li>')).append('店內電話:' + msg[i]['shop_tel']));
    var shop_address = $($('<h4>').attr({'class': 'w3-padding-left w3-margin-left'})).append($($('<li>')).append('店內地址:' + msg[i]['shop_address']));
    var div = $($('<div>').attr({'class': 'w3-left-align w3-padding'}))
        .append(shop_owner)
        .append(ui_phone)
        .append(shop_tel)
        .append(shop_address);
    
    $('#a0').append(
        $($('<div>').attr({'class': 'w3-card-4 w3-center'}))
            .append(shop_name)
            .append(div)
    );

        }
    })
    .fail(function() {
        alert('query failed!');
    });
});