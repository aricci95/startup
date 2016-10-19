$(document).ready(function() {
    $(".notification .unread").click(function (e) {
        e.preventDefault();

        var notification = $(e.target).closest('.notification');

        $.post("notification/read", { notificationId : notification.attr('data-id') }, function(data) {
            window.location.href = $(e.target).attr('data-url');
        });
    });
});