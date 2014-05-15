<?php

@set_time_limit(0);
require_once 'functions.php';

if (isset($_GET['action'])) {
    extract($_GET);
    if ($action == 'test_db_conn') {
        if (test_db_conn($server, $username, $password, $database_name)) {
            echo 1;
        } else {
            echo 0;
        }
    }
} elseif (isset($_POST['action'])) {
    extract($_POST);
    if ($action == 'make_config_file') {
        $dir = dirname(getcwd()) . '/include/';
        $filename = $dir . 'db_defines.php';
        $handle = fopen($filename, 'w');
        if ($handle) {
            $data = '<?php
define(\'database_type\', \'mysql\');
define(\'server\', \'' . $server . '\');
define(\'username\', \'' . $username . '\');
define(\'password\', \'' . $password . '\');
define(\'database_name\', \'' . $database_name . '\');
date_default_timezone_set(\'' . $timezone . '\');
';
            fwrite($handle, $data);
            fclose($handle);
            echo 1;
        } else {
            echo 0;
        }
        //pr($_POST);
    }
}
