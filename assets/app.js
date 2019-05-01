// rlx

$("document").ready(function() {
    $(".rlxSubmit").click(function() {
        var data;
        $('.rlx_error').remove();
        $('.rlx_success').remove();
        data = $('.rlxForm').serialize();

        $('.rlxForm').addClass('loading');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "ajax-accept.php",
            data: data,
            success: function(data) {
                if (data.status == "error") {
                    $('.rlx_column').prepend('<div class="ui negative message rlx_error"><div class="header">Error!</div><p>' + data.message + '</p></div>').fadeIn();
                    $('.rlxForm').removeClass('loading');
                } else if (data.status == "success") {
                    $('.rlxForm')[0].reset();
                    $('.rlx_error').remove();
                    $('.rlx_column').prepend('<div class="ui positive message rlx_success"><div class="header">Success!</div><p>' + data.message + '</p></div>').fadeIn();
                    $('.rlxTbody').append('<tr><td data-label="#">' + data.data.id + '</td><td data-label="Name">' + data.data.firstname + '</td><td data-label="Lastname">' + data.data.lastname + '</td><td data-label="Username">' + data.data.username + '</td><td data-label="Email">' + data.data.email + '</td></tr>').fadeIn();
                    $('.rlxForm').removeClass('loading');
                }
            }
        })
    });
});