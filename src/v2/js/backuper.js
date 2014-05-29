(function($) {
    $.fn.enable = function() {
        this.attr('disabled', false);
    };
    $.fn.disable = function() {
        this.attr('disabled', true);
    };
}(jQuery));

//my alert
var my_alert = function(options) {
    $('#alert-message-container').remove();
    my_alert.init();
    var opts = $.extend({
        type: 'success',
        status: 'Success:',
        message: 'success message',
        delay: typeof options.delay === 'number' ? options.delay : 3000
    }, options);
    var alert_message_container = $('#alert-message-container');
    alert_message_container.removeClass("hidden");
    var alert_message_div = alert_message_container.find('#alert-message');
    var alert_message_status = alert_message_div.find('#alert-message-status');
    var alert_message_text = alert_message_div.find('#alert-message-text');
    if (opts.type === 'success') {
        alert_message_div.addClass('alert-success');
    }
    if (opts.type === 'danger') {
        alert_message_div.addClass('alert-danger');
    }
    if (opts.type === 'warning') {
        alert_message_div.addClass('alert-warning');
    }
    if (opts.status === '@spinner') {
        opts.status = '<i class="fa fa-refresh fa-spin"></i>';
    }
    alert_message_status.html(opts.status);
    alert_message_text.html(opts.message);
    alert_message_div.find('#alert-message-close').on('click', function() {
        alert_message_container.addClass("hidden");
        $(this).next().html('').next().html('');
        $(this).parent().removeClass('alert-danger alert-success alert-warning');
    });
    if (opts.delay !== 0) {
        setTimeout(function() {
            alert_message_div.find('#alert-message-close').click();
        }, opts.delay);
    }
};
my_alert.init = function() {
    var alert_html = '<div class="container hidden" id="alert-message-container" style="position:fixed;width:100%;z-index:999"><div id="alert-message" class="alert alert-dismissable"><button id="alert-message-close" type="button" class="close"><i class="fa fa-times-circle"></i></button><strong id="alert-message-status"></strong> <span id="alert-message-text"></span></div></div>';
    $('body').prepend(alert_html);
};
my_alert.init();
