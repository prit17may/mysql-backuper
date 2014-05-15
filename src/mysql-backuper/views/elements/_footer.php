<?php footer() ?>
<script src="<?php js_url('jquery/jquery.min.js') ?>"></script>
<script src="<?php js_url('bootstrap/bootstrap.min.js') ?>"></script>
<script src="<?php js_url('backuper.js') ?>"></script>
<script>
    $(document).ready(function() {
        //my alert
        var my_alert = function(options) {
            var opts = $.extend({
                type: 'success',
                status: 'Success:',
                message: 'success message',
                delay: typeof options.delay === 'number' ? options.delay : 3000
            }, options);
            var alert_message_div = $('#alert-message');
            var alert_message_status = alert_message_div.find('#alert-message-status');
            var alert_message_text = alert_message_div.find('#alert-message-text');
            if (opts.type === 'success') {
                alert_message_div.addClass('alert-success').removeClass('hidden');
            }
            if (opts.type === 'danger') {
                alert_message_div.addClass('alert-danger').removeClass('hidden');
            }
            alert_message_status.text(opts.status);
            alert_message_text.html(opts.message);
            alert_message_div.find('#alert-message-close').on('click', function() {
                $(this).next().html('').next().html('');
                $(this).parent().addClass('hidden').removeClass('alert-danger alert-success');
            });
            setTimeout(function() {
                alert_message_div.find('#alert-message-close').click();
            }, opts.delay);
        };
        my_alert.init = function() {
            var alert_html = '<div class="container" style="padding:0px 15px"><div id="alert-message" class="alert alert-dismissable hidden"><button id="alert-message-close" type="button" class="close"><i class="fa fa-times-circle"></i></button><strong id="alert-message-status"></strong> <span id="alert-message-text"></span></div></div>';
            $('body').append(alert_html);
        };
        my_alert.init();
        //my alert ends
        function log(e) {
            console.clear();
            console.log(e);
        }
        var ajax_url = '<?php echo base_url('ajax') ?>';
        var wait_html = '<i class="fa fa-refresh fa-spin"></i> Please wait...';

        $('[data-toggle="tooltip"]').tooltip();
        $('#del_db_config').on('click', function() {
            var btn = $('#del_db_config');
            var html = btn.html();
            if (confirm("This will also delete all backups created previously...\nAre You sure???")) {
                btn.html(wait_html).disable();
                $.post(ajax_url, {action: 'del_db_config'}, function(data) {
                    if (data === '1') {
                        my_alert({
                            message: "File Deleted Successfully, Redirecting you to configuration page",
                            delay: 4000
                        });
                        setTimeout(function() {
                            window.location = '<?php echo base_url('config/config.php') ?>';
                        }, 3000);
                    } else {
                        my_alert({
                            type: 'danger',
                            status: 'Error:',
                            message: "Some Error occoured",
                            delay: 2000
                        });
                    }
                    btn.html(html).enable();
                });
            }
        });
        $('#backup_now').on('click', function() {
            backup_now();
        });
        $('#refresh_backups').on('click', function() {
            refresh_backups('');
        });
        $('#refresh_date_backups').on('click', function() {
            open_backups($(this).attr('data-date'));
        });
        function refresh_backups(selected_date) {
            selected_date = typeof selected_date === 'string' ? selected_date : '';
            var table = $('table#backup_dates_table');
            var table_html = '<thead></thead><tbody></tbody>';
            table.html(table_html);
            var thead = table.find('thead');
            var thead_html = '<tr class="success text-info"><th style="width:10%" class="text-right">S.No</th><th class="text-center">Backup Date</th><th width="15%">Actions</th></tr>';
            var tbody = table.find('tbody');
            var tbody_html = '<tr><th colspan="3" class="text-center"><div class="alert alert-danger" style="padding:5px;margin-bottom:0px">No Backups Found...<br />Try <a id="backup_now" href="javascript:void(0);">Backup Now</a>...</div></th></tr>';
            var backup_dates = sync_call(ajax_url, {action: 'get_backup_dates'});
            if (typeof backup_dates == 'object' && !$.isEmptyObject(backup_dates)) {
                var new_html = '';
                var i = 1;
                $.each(backup_dates, function(k, v) {
                    new_html += '<tr>';
                    new_html += '<td class="text-right">' + i + '</td>';
                    new_html += '<td class="text-center"><a id="open_backups" href="javascript:void(0);" title="Open backups for this date" data-date="' + v + '">' + v + '</a></td>';
                    new_html += '<td><a id="delete_backups" href="javascript:void(0);" title="Delete all backups for this date" data-date="' + v + '">Delete</a></td>';
                    new_html += '</tr>';
                    i++;
                });
                tbody_html = new_html;
            }
            thead.html(thead_html);
            tbody.html(tbody_html);
            if (typeof backup_dates == 'object' && !$.isEmptyObject(backup_dates)) {
                tbody.find('a#open_backups').on('click', function() {
                    tbody.find('tr').removeClass("info");
                    $(this).parent().parent().addClass("info");
                    open_backups($(this).attr('data-date'));
                });
                tbody.find('a#delete_backups').on('click', function() {
                    var date = $(this).attr('data-date');
                    if (confirm('This will Permanently delete all Backups for ' + date)) {
                        if (delete_backups(date) === '1') {
                            refresh_backups('');
                            my_alert({
                                message: 'Backups deleted successfully...'
                            });
                        }
                    }
                });
                if (selected_date) {
                    tbody.find('tr').removeClass("info");
                    tbody.find('[data-date="' + selected_date + '"]').parent().parent().addClass("info");
                }
            } else {
                tbody.find('#backup_now').on('click', function() {
                    backup_now();
                });
            }
            open_backups(selected_date);
        }
        function open_backups(date) {
            if (date) {
                $('#refresh_date_backups').attr('data-date', date);
                var date_backups = sync_call(ajax_url, {action: 'get_date_backups', date: date});
                var num_backups = date_backups.length;
            } else {
                var date_backups = [];
                var num_backups = '0';
            }
            $('#num_backups').text(' (' + num_backups + ')');
            var table = $('table#date_backups_table');
            var table_html = '<thead></thead><tbody></tbody>';
            table.html(table_html);
            var thead = table.find('thead');
            var thead_html = '<tr class="success text-info"><th style="width:10%" class="text-right">S.No</th><th class="text-center">Backup Name</th><th width="15%">Actions</th></tr>';
            var tbody = table.find('tbody');
            var tbody_html = '<tr><th colspan="3" class="text-center"><div class="alert alert-danger" style="padding:5px;margin-bottom:0px">';
            if (date) {
                tbody_html += 'No Backup found for Selected Date<br />Try <a id="backup_now" href="javascript:void(0);">Backup Now</a>...';
            } else {
                tbody_html += 'No Date Selected...<br />Try selecting a date from Previous Backup Dates Panel...';
            }
            tbody_html += '</div></th></tr>';
            if (typeof date_backups == 'object' && !$.isEmptyObject(date_backups)) {
                var new_html = '';
                var i = 1;
                $.each(date_backups, function(k, v) {
                    new_html += '<tr>';
                    new_html += '<td class="text-right">' + i + '</td>';
                    new_html += '<td class="text-center"><a id="restore_backup" href="javascript:void(0);" title="Restore this Backup" data-backup="' + v + '">' + v + '</a></td>';
                    new_html += '<td><a id="delete_backup" href="javascript:void(0);" title="Delete this Backup" data-backup="' + v + '">Delete</a></td>';
                    new_html += '</tr>';
                    i++;
                });
                tbody_html = new_html;
            }
            thead.html(thead_html);
            tbody.html(tbody_html);
            if (typeof date_backups == 'object' && !$.isEmptyObject(date_backups)) {
                tbody.find('a#restore_backup').on('click', function() {
                    var btn = $('#backup_now');
                    var btn_html = btn.html();
                    btn.html(wait_html).disable();
                    var backup = $(this).attr('data-backup');
                    $.post(ajax_url, {action: 'restore_backup', date: date, backup: backup}, function(data) {
                        btn.html(btn_html).enable();
                        if (data === '1') {
                            my_alert({
                                type: 'success',
                                status: 'Success:',
                                message: 'Database Restored to selected backup...',
                                delay: 2000
                            });
                        } else {
                            my_alert({
                                type: 'danger',
                                status: 'Error:',
                                message: 'Restore Failed, Try Again...',
                                delay: 2000
                            });
                        }
                    });
                });
                tbody.find('a#delete_backup').on('click', function() {
                    var backup = $(this).attr('data-backup');
                    if (confirm('This will Permanently delete ' + backup)) {
                        if (delete_backup(date, backup) === '1') {
                            open_backups(date);
                            my_alert({
                                message: 'Backup deleted successfully...'
                            });
                        }
                    }
                });
            } else {
                tbody.find('#backup_now').on('click', function() {
                    backup_now();
                });
            }
        }
        function delete_backups(date) {
            return sync_call(ajax_url, {action: 'delete_backups', date: date}, true, true);
        }
        function delete_backup(date, backup) {
            return sync_call(ajax_url, {action: 'delete_backup', date: date, backup: backup}, true, true);
        }
        function backup_now() {
            var selected_date = $('table#backup_dates_table').find('tr.info a[data-date]').attr('data-date');
            var btn = $('#backup_now');
            var html = btn.html();
            btn.html(wait_html).disable();
            $.post(ajax_url, {action: 'backup_now'}, function(data) {
                btn.html(html).enable();
                if (data === '1') {
                    my_alert({
                        message: 'Backup Taken Successfully...'
                    });
                    refresh_backups(selected_date);
                } else {
                    my_alert({
                        type: 'danger',
                        status: 'Error:',
                        message: 'Backup Failed, Check permissions for Backuper...',
                        delay: 2000
                    });
                }
            });
        }
        function sync_call(url, data, post_call, original_response) {
            post_call = typeof post_call !== 'undefined' ? post_call : false;
            original_response = typeof original_response !== 'undefined' ? original_response : false;
            $.ajaxSetup({
                async: false
            });
            if (post_call === true) {
                if (original_response === true)
                    return $.post(url, data).responseText;
                return $.parseJSON($.post(url, data).responseText);
            }
            if (original_response === true)
                return $.get(url, data).responseText;
            return $.parseJSON($.get(url, data).responseText);
        }
        refresh_backups('');
    });
</script>
</body>
</html>
