/**
 *  MENU
**/
$(document).on('click', '#windows_icon, #alert_icon', function(e) {
    e.preventDefault();
    $('#' + $(e.target).attr('menu') + '_menu').fadeToggle(200);
});

$(document).on('click', '.content', function(e) {
    $('#windows_menu, #notifications_menu').fadeOut(200);
});

$(document).ready(function() {
    render('staff');
});

function render(url) {
    $('.content').hide();
    $.get(url, function(response) {
        $('.content').html(response).fadeIn(200);
    });
}