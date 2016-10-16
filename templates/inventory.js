$(document).ready(function(){
  $('button').on('click', function() {
    console.log($(this).parent().parent());
    $(this).parent().parent().css('display', 'none');
  })
});