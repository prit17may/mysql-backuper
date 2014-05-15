<?php

function vd($val) {
    echo "<pre>";
    var_dump($val);
    echo "</pre>";
}

function pr($val, $var_name = NULL) {
    if (!empty($val)) {
        echo "<pre>";
        if (isset($var_name)) {
            echo "<div style='border:solid 2px red;color:lightgreen;background-color:black;font-size:18px;padding:5px;border-radius:5px'>Data for '<font color=red>" . $var_name . "</font>' as [key] => value</div><br>";
        }
        print_r($val);
        echo "</pre>";
    }
}

function pj($Array, $pp = FALSE) {
    header('Content-type: text/plain');
    if ($pp === TRUE)
        echo json_encode($Array, JSON_PRETTY_PRINT);
    else
        echo json_encode($Array);
}

function caps_keys($Array, $make_lower = FALSE, $include_children = FALSE) {
    if (is_array($Array)) {
        $cap_keys = array();
        foreach ($Array as $k => $v) {
            if ($include_children === TRUE) {
                if (is_array($v)) {
                    if ($make_lower === TRUE)
                        $cap_keys[strtolower($k)] = caps_keys($v, $make_lower, $include_children);
                    else
                        $cap_keys[strtoupper($k)] = caps_keys($v, $make_lower, $include_children);
                    unset($Array[$k]);
                } else {
                    if ($make_lower === TRUE)
                        $cap_keys[strtolower($k)] = $v;
                    else
                        $cap_keys[strtoupper($k)] = $v;
                    unset($Array[$k]);
                }
            } else {
                if ($make_lower === TRUE)
                    $cap_keys[strtolower($k)] = $v;
                else
                    $cap_keys[strtoupper($k)] = $v;
                unset($Array[$k]);
            }
        }
        $Array = $cap_keys;
    }
    return $Array;
}

function funcs($opts /* internal(T,F), natural(T,F), sort(T,F), return(T,F) */ = array()) {
    if (!empty($opts)) {
        $opts = caps_keys($opts, TRUE);
    }
    $funcs = get_defined_functions();
    if (isset($opts['internal']) && $opts['internal'] === TRUE) {
        $funcs = $funcs['internal'];
    } else {
        $funcs = $funcs['user'];
    }
    if (isset($opts['natural']) && $opts['natural'] === TRUE) {
        foreach ($funcs as $k => $v) {
            $funcs[$k + 1] = $v;
        }
        unset($funcs[0]);
    }
    if (isset($opts['sort']) && $opts['sort'] === TRUE)
        asort($funcs);
    if (isset($opts['return']) && $opts['return'] === TRUE) {
        return $funcs;
    }
    pr($funcs);
}

function clean($str) {
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    $str = strip_tags($str);
    return $str;
}

function bulk_clean($Array, $clean_contents = FALSE) {
    if (is_array($Array) && !empty($Array)) {
        foreach ($Array as $k => $v) {
            if ($clean_contents === TRUE) {
                if (is_array($v)) {
                    $v = bulk_clean($v, TRUE);
                } else {
                    $v = clean($v);
                }
            } else {
                $v = clean($v);
            }
            $Array[$k] = $v;
        }
    }
    return $Array;
}

function test_db_conn($server, $username, $password, $database_name) {
    $conn = @mysql_connect($server, $username, $password);
    if (is_resource($conn)) {
        if (@mysql_select_db($database_name))
            return TRUE;
    }
    return FALSE;
}

function formatOffset($offset) {
    $hours = $offset / 3600 - 1;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 AND $minutes == 0) {
        $sign = ' ';
    }
    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT)
            . ':' . str_pad($minutes, 2, '0');
}

function timezones() {
    $list = DateTimeZone::listAbbreviations();
    $idents = DateTimeZone::listIdentifiers();

    $data = $offset = $added = array();
    foreach ($list as $abbr => $info) {
        foreach ($info as $zone) {
            if (!empty($zone['timezone_id']) && !in_array($zone['timezone_id'], $added) && in_array($zone['timezone_id'], $idents)) {
                $z = new DateTimeZone($zone['timezone_id']);
                $c = new DateTime(null, $z);
                $zone['time'] = $c->format('H:i (a)');
                $data[] = $zone;
                $offset[] = $z->getOffset($c);
                $added[] = $zone['timezone_id'];
            }
        }
    }

    array_multisort($offset, SORT_DESC, $data);
    $options = array();
    foreach ($data as $key => $row) {
//        $options[$row['timezone_id']] = /*'(' . formatOffset($row['offset']) . ') - ' . */$row['time'].' - '.$row['timezone_id'];
        $options[$row['timezone_id']] = $row['time'] . ' - ' . formatOffset($row['offset']) . ' - ' . $row['timezone_id'];
    }
    asort($options);
    return $options;
}
