$(document).on('click', '.staff .entry', function(e) {
    e.preventDefault();

    $.post("staff/show", { employeeId : $(e.target).attr('data-id') }, function(data) {
        if (!$('#windows_menu').is(':visible')) {
            $('#windows_menu').toggle();
        }

        $('#windows_menu').html(data);
    });
});