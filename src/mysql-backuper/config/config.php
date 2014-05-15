<?php
require_once '../include/url_defines.php';
require_once 'functions.php';
$timezones = timezones();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>One Time Configuration</title>
        <link href="<?php echo main_url ?>css/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo main_url ?>css/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="<?php echo main_url ?>css/style.css" rel="stylesheet">
        <link href="<?php echo main_url ?>css/fa/css/font-awesome.min.css" rel="stylesheet">

    </head>
    <body>

        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand">Backuper &amp; Restorer</a>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h1>Database Configuration Page</h1>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <form role="form">
                        <div class="form-group">
                            <label for="server">Server</label>
                            <input autofocus type="text" class="form-control" id="server" placeholder="eg: localhost">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="eg: root">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" id="password" placeholder="eg: password">
                        </div>
                        <div class="form-group">
                            <label for="database_name">Database Name</label>
                            <input type="text" class="form-control" id="database_name" placeholder="eg: database">
                        </div>
                        <div class="form-group">
                            <label for="timezone">Time Zone</label>
                            <select id="timezone" class="form-control">
                                <option hidden value="">Select Time Zone</option>
                                <?php
                                foreach ($timezones as $identifier => $name) {
                                    echo '<option value="' . $identifier . '">' . $name . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <button id="test_db_conn" type="button" class="btn btn-warning"><i class="fa fa-gears"></i> Test Connection</button><span id="message" class="hidden" style="margin-left:10px"></span>
                    </form>
                </div>
            </div>
        </div>
        <div id="footer">
            <div class="container">
                <p class="text-muted">
                    Designed &amp; Developed by <a href="https://www.facebook.com/pritpalsingh.in" target="_blank">Pritpal Singh</a>
                    <span class="pull-right">Designed Using <a href="http://getbootstrap.com" target="_blank">Bootstrap</a></span>
                </p>
            </div>
        </div>
        <?php
        include './footer.php';
        
