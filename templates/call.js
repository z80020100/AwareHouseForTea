$(document).ready(function(){
  $('#submit').on('click', function() {
    $('input:checked').each(function() {
      console.log($(this).parent().parent().find('input[type=number]').data('name'));
      alert('已經叫料');
    });
  });
});