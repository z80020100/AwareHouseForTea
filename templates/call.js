$(document).ready(function(){
  $("select").on('click', function() {
    if (this.value == -1) {
      $('select[name=item]').css('display', 'none');
      $('input[name=item]').css('display', 'inline-block');
    }
  });

  $('input[type=submit]').on('click', function() {
    var item1 = $('select[name=item] option:selected').val();
    var item2 = $.trim($('input[name=item]').val());
    var num = $.trim($('input[name=number]').val());
    if(((item1 && item1 != -1) || item2 != '') && $.isNumeric(num)) {
      alert('已經叫料');
    }
    else {
      alert('資料有誤');
    }
  });
});