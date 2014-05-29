<script src="//code.jquery.com/jquery-latest.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="<?php echo main_url ?>js/backuper.js"></script>
<script>
    $(document).ready(function() {
        //default coding
        function error(e) {
            console.error(e);
        }
        function log(e) {
            console.log(e);
        }
        //script coding
        var ajax_url = '<?php echo main_url ?>config/ajax.php';
        $('#test_db_conn').on('click', function() {
            var timezone = $('#timezone').val();
            if (timezone) {
                var server = $('#server').val();
                var username = $('#username').val();
                var password = $('#password').val();
                var database_name = $('#database_name').val();
                var test_button = $('#test_db_conn');
                test_button.disable();
                $('#server').disable();
                $('#username').disable();
                $('#password').disable();
                $('#database_name').disable();
                $('#timezone').disable();
                var msg_container = $('#message');
                msg_container.text('Testing Connection to database, Please wait...').removeClass("hidden");
                $.get(ajax_url, {action: 'test_db_conn', server: server, username: username, password: password, database_name: database_name}, function(data) {
                    if (data === '1') {
                        msg_container.html('Connection Successful, <a id="continue" href="javascript:void(0);" data-toggle="tooltip" title="save credentials and continue to Backuper">continue</a>... or <a id="change" href="javascript:void(0);">change credentials</a>');
                        $('[data-toggle="tooltip"]').tooltip();
                        $('#change').on('click', function() {
                            test_button.enable();
                            $('#server').enable();
                            $('#username').enable();
                            $('#password').enable();
                            $('#database_name').enable();
                            $('#timezone').enable();
                            msg_container.text('').addClass('hidden');
                        });
                        $('#continue').on('click', function() {
                            $.post(ajax_url, {action: 'make_config_file', server: server, username: username, password: password, database_name: database_name, timezone: timezone}, function(data) {
                                if (data === '1') {
                                    alert("Configuration Successful, Redirecting you to Backuper...");
                                    window.location = '<?php echo main_url ?>';
                                } else {
                                    msg_container.text("Permission Denied by System, Please allow permissions for Backuper to add and edit files...");
                                }
                            });
                        });
                    } else {
                        test_button.enable();
                        $('#server').enable();
                        $('#username').enable();
                        $('#password').enable();
                        $('#database_name').enable();
                        $('#timezone').enable();
                        msg_container.text('Connetion Failed, Check Credentials...');
                    }
                });
            } else {
                alert('Please Select your time Zone...');
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
</body>
</html>
