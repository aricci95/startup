$(document).on('click', '.staff .entry', function(e) {
    e.preventDefault();

    $.post("staff/show", { employeeId : $(e.target).attr('data-id') }, function(data) {
        $('#windows_menu').html(data);

        if (!$('#windows_menu').is(':visible')) {
            $('#windows_menu').fadeIn(200);
        }
    });
});