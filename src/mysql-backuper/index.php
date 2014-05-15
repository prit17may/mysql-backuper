<?php

define('root', getcwd() . '/');
require_once root . 'include/functions.php';

require_once root . '/include/url_defines.php';
if (!file_exists(root . '/include/db_defines.php')) {
    header("Location: " . base_url('config/config.php'));
    exit;
}
require_once root . '/include/db_defines.php';
require_once root . 'include/controller.php';
//call the controller class
$config = array(
    'database_type' => database_type,
    'database_name' => database_name,
    'server' => server,
    'username' => username,
    'password' => password
);
$obj = new Controller($config);

$database = $obj->_db;
$obj->_data['obj'] = $obj;
$obj->_data['database'] = $database;
$vars = array();

if (isset($_GET['vars']) && $_GET['vars']) {
    $vars = $_GET['vars'];
    get_vars($vars);
}
$obj->_data['vars'] = $vars;

if (isset($vars) && !empty($vars) && $vars[1]) {
    switch ($vars[1]) {
        case 'ajax':
            $obj->view('ajax');
            break;
        default :
            $obj->view('elements/404', '', TRUE);
            break;
    }
} else {
    $obj->view('home', '', TRUE);
}
