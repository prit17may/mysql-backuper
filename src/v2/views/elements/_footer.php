<?php footer() ?>
<script src="//code.jquery.com/jquery-latest.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="<?php js_url('backuper.js') ?>"></script>
<script>
    $(document).ready(function() {

        var ajax_url = '<?php echo base_url('ajax') ?>';
        var wait_html = '<i class="fa fa-refresh fa-spin"></i> Please wait...';

        $('[data-toggle="tooltip"],.tip').tooltip();

        function log(e, clear) {
            clear = typeof clear === 'boolean' ? clear : false;
            if (clear === true)
                console.clear();
            console.log(e);
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

<?php if (isset($vars[1]) && $vars[1] === 'tables') { ?>
            function all_tables() {
                var table = $('table#all_tables');
                var table_html = '<thead></thead><tbody></tbody>';
                table.html(table_html);
                var thead = table.find('thead');
                var tbody = table.find('tbody');
                var thead_html = '<tr class="success text-info"><th style="width:10%" class="text-center">S.No</th><th class="text-center">Table Name(Rows)</th><th style="min-width:177px;width:177px">Actions</th></tr>';
                var tbody_html = '<tr><td colspan="3">No Tables Found</td></tr>';
                var tables = sync_call(ajax_url, {action: 'get_tables'});

                if (typeof tables === 'object' && !$.isEmptyObject(tables)) {
                    tbody_html = '';
                    $.each(tables, function(k, v) {
                        tbody_html += '<tr data-table="' + v.Name + '"><td class="text-center">' + (k + 1) + '</td><td style="text-align:center"><b><a href="<?php echo base_url('table') ?>/' + v.Name + '">' + v.Name + '</a> <span title="Number of Rows" class="tip">(' + v.Rows + ')</span></b></td><td><select id="options"><option value="" hidden>Select Action</option>';
                        tbody_html += v.Rows > 0 ? '<option value="download">Download CSV</option>' : '';
                        tbody_html += '<option value="delete">Delete</option></select><button class="btn-xs pull-right" id="table_opts" data-table="' + v.Name + '">Done</button></td></tr>';
                    });
                }

                thead.html(thead_html);
                tbody.html(tbody_html);

                $('[data-toggle="tooltip"],.tip').tooltip();

                $('select#options').on('change', function() {
                    var val = $(this).val();
                    $('select#options').val('');
                    $(this).val(val).focus();
                });

                $('button#table_opts').on('click', function() {
                    var val = $(this).prev().val();
                    if (val) {
                        var table_name = $(this).attr("data-table");

                        if (val === 'delete') {
                            if (confirm("Are You Sure, You want to delete table " + table_name + ' ?')) {
                                my_alert({
                                    type: 'warning',
                                    status: '@spinner',
                                    message: 'Deleting Table, Please wait...',
                                    delay: 0
                                });
                                $.post(ajax_url, {action: 'delete_table', table_name: table_name}, function(data) {
                                    if (data === '1') {
                                        tbody.find('tr[data-table="' + table_name + '"]').hide(500);
                                        setTimeout(all_tables, 500);
                                        my_alert({
                                            message: 'Table deleted successfully...',
                                            delay: 2000
                                        });
                                    } else {
                                        my_alert({
                                            type: 'danger',
                                            status: 'Error:',
                                            message: 'Table not deleted, Try again...',
                                            delay: 1500
                                        });
                                    }
                                });
                            }
                        }
                        if (val === 'download') {
                            window.top.location = ajax_url + '?action=download&table_name=' + table_name;
                        }
                        $('select#options').val('');
                    }
                });

            }
            all_tables();
<?php } elseif (isset($vars[1]) && $vars[1] === 'table') { ?>

<?php } elseif (isset($vars[1]) && $vars[1] === 'table_config') { ?>

            $('a#check_all').on('click', function() {
                $(this).hide().parent().find('#uncheck_all').show();
                $(this).parent().parent().next().find('input[type="checkbox"]:enabled').prop("checked", true);
            });

            $('a#uncheck_all').on('click', function() {
                $(this).hide().parent().find('#check_all').show().find('i').addClass('fa-square-o').removeClass('fa-check-square-o');
                $(this).parent().parent().next().find('input[type="checkbox"]:enabled').prop("checked", false);
            });

            $('input[type="checkbox"]:enabled').on('change', function() {
                var all = $(this).parent().parent().parent().find('input[type="checkbox"]:enabled');
                all = all.length;
                var notchecked = $(this).parent().parent().parent().find('input[type="checkbox"]:enabled:not(:checked)');
                var checked = $(this).parent().parent().parent().find('input[type="checkbox"]:enabled:checked');
                if (checked.length) {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').find('i').addClass('fa-check-square-o').removeClass('fa-square-o');
                } else {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').find('i').addClass('fa-square-o').removeClass('fa-check-square-o');
                }
                if (notchecked.length == 0) {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').click();
                } else {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').show().next().hide();
                }
                $.each(checked, function() {
                    $(this).parent().css({
                        'color': '#fff',
                        'background': '#000'
                    });
                });
                $.each(notchecked, function() {
                    $(this).parent().css({
                        'color': '#000',
                        'background': '#fff'
                    });
                });
            }).each(function() {
                var all = $(this).parent().parent().parent().find('input[type="checkbox"]:enabled');
                all = all.length;
                var notchecked = $(this).parent().parent().parent().find('input[type="checkbox"]:enabled:not(:checked)');
                var checked = $(this).parent().parent().parent().find('input[type="checkbox"]:enabled:checked');
                if (checked.length) {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').find('i').addClass('fa-check-square-o').removeClass('fa-square-o');
                } else {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').find('i').addClass('fa-square-o').removeClass('fa-check-square-o');
                }
                if (notchecked.length == 0) {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').click();
                } else {
                    $(this).parent().parent().parent().parent().parent().prev().find('#check_all').show().next().hide();
                }
                $.each(checked, function() {
                    $(this).parent().css({
                        'color': '#fff',
                        'background': '#000'
                    });
                });
                $.each(notchecked, function() {
                    $(this).parent().css({
                        'color': '#000',
                        'background': '#fff'
                    });
                });
            });

            $('button#save_table_config').on('click', function() {
                var table_name = $(this).attr('data-table_name');
                var button = $('button#save_table_config[data-table_name="' + table_name + '"]');
                var html = button.html();
                var cols = [];
                button.parent().parent().next().find('input[type="checkbox"]:enabled:checked').each(function() {
                    cols.push($(this).val());
                });
                button.html(wait_html).disable();
                my_alert({
                    type: 'warning',
                    status: '@spinner',
                    message: 'Saving Table Configuration, Please wait...',
                    delay: 0
                });
                $.post(ajax_url, {action: 'save_table_config', table_name: table_name, cols: cols}, function(data) {
                    button.html(html).enable();
                    if (data === '1') {
                        my_alert({
                            message: 'Saved for table ' + table_name + ' ...'
                        });
                    } else {
                        my_alert({
                            type: 'danger',
                            status: 'Error:',
                            message: 'Some Error Occoured, Please try again...'
                        });
                    }
                });
            });

            $('#save_all_table_config').on('click', function() {
                $('button#save_table_config').click();
            });
<?php } else { ?>
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
                        var backup = $(this).attr('data-backup');
                        my_alert({
                            type: 'alert',
                            status: '@spinner',
                            message: 'Restoring Backup, Please wait...',
                            delay: 0
                        });
                        $.post(ajax_url, {action: 'restore_backup', date: date, backup: backup}, function(data) {
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
                my_alert({
                    type: 'warning',
                    status: '@spinner',
                    message: 'Creating Backup, Please wait...',
                    delay: 0
                });
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

            refresh_backups('');

<?php } ?>
        $('#del_db_config').on('click', function() {
            var btn = $('#del_db_config');
            var html = btn.html();
            if (confirm("This will also delete all backups created previously...\nAre You sure???")) {
                btn.html(wait_html).disable();
                my_alert({
                    type: 'warning',
                    status: '@spinner',
                    message: 'Deleting Configuration File, Please wait...',
                    delay: 0
                });

                $.post(ajax_url, {action: 'del_db_config'}, function(data) {
                    if (data === '1') {
                        my_alert({
                            message: "File Deleted Successfully, Redirecting you to configuration page...",
                            delay: 4000
                        });
                        setTimeout(function() {
                            window.location = '<?php echo base_url('config/config.php') ?>';
                        }, 2000);
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
    });
</script>
</body>
</html>
