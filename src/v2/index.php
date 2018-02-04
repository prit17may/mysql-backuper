<?php

session_start();
ob_start();

define('root', str_replace('\\', '/', getcwd()).'/');
require_once root.'include/functions.php';

require_once root.'include/url_defines.php';
if (!file_exists(root.'/include/db_defines.php')) {
    header('Location: '.base_url('config/config.php'));
    exit;
}
if (!is_dir(root.'backups/')) {
    mkdir(root.'backups/', 0777, true);
}
require_once root.'/include/db_defines.php';
require_once root.'include/controller.php';

//call the controller class
$config = [
    'database_type' => database_type,
    'database_name' => database_name,
    'server'        => server,
    'username'      => username,
    'password'      => password,
];
$obj = new Controller($config);

$database = $obj->_db;
$tables = get_tables($database);
$table_config = [];
foreach ($tables as $table_name) {
    $table_config[$table_name] = [];
}
set_table_config($table_config, true);
$obj->_data['obj'] = $obj;
$obj->_data['database'] = $database;
$vars = [];

if (isset($_GET['vars']) && $_GET['vars']) {
    $vars = $_GET['vars'];
    get_vars($vars);
}
$obj->_data['vars'] = $vars;
$without_hf = [
    'ajax',
];

if (isset($vars) && !empty($vars) && $vars[1]) {
    if (in_array($vars[1], $without_hf)) {
        $obj->view($vars[1]);
    } else {
        $obj->view($vars[1], '', true);
    }
} else {
    $obj->view('home', '', true);
}
ob_flush();
