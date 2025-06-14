$(function () {
    $('#popup_close, #popup_overlay').on('click', function () {
      $('#popup_box').animate({ 'top': '-2000px' }, 500, function () {
        $('#popup_overlay').fadeOut('fast');
      });
    });
    $("#popup_open").on('click', function () {
      $('#popup_overlay').fadeIn('fast', function () {
        $('#popup_box').animate({ 'top': '0' }, 500);});
    });
  });