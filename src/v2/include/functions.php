<?php

function css_url($uri = '')
{
    echo base_url('css/'.$uri);
}

function js_url($uri = '')
{
    echo base_url('js/'.$uri);
}

function img_url($uri = '')
{
    echo base_url('images/'.$uri);
}

function base_url($uri = '')
{
    return main_url.$uri;
}

function make_slug($value, $opts = null)
{
    $value = preg_replace('/@/', ' at ', $value);
    $value = preg_replace('/£/', ' pound ', $value);
    $value = preg_replace('/#/', ' hash ', $value);
    $value = preg_replace("/[\-+]/", ' ', $value);
    $value = preg_replace("/[\s+]/", ' ', $value);
    $value = preg_replace("/[\.+]/", '', $value);
    $value = preg_replace("/[^A-Za-z0-9\.\s]/", '', $value);
    $value = preg_replace("/[\s]/", '-', $value);
    $value = preg_replace("/\-\-+/", '-', $value);
    $value = strtolower($value);
    if (substr($value, -1) == '-') {
        $value = substr($value, 0, -1);
    }
    if (substr($value, 0, 1) == '-') {
        $value = substr($value, 1);
    }
    if (isset($opts['LIMIT']) && is_numeric($opts['LIMIT']) && $opts['LIMIT'] > 0) {
        $value = substr($value, 0, $opts['LIMIT']);
    }

    return $value;
}

function vd($val)
{
    echo '<pre>';
    var_dump($val);
    echo '</pre>';
}

function pr($val, $var_name = null)
{
    if (!empty($val)) {
        echo '<pre>';
        if (isset($var_name)) {
            echo "<div style='border:solid 2px red;color:lightgreen;background-color:black;font-size:18px;padding:5px;border-radius:5px'>Data for '<font color=red>".$var_name."</font>' as [key] => value</div><br>";
        }
        print_r($val);
        echo '</pre>';
    }
}

function pj($Array, $pp = false)
{
    header('Content-type: text/plain');
    if ($pp === true) {
        echo json_encode($Array, JSON_PRETTY_PRINT);
    }
//        echo json_encode($Array, JSON_PRETTY_PRINT);
    else {
        echo json_encode($Array);
    }
}

function ppj($Array)
{
    header('Content-type: text/plain');
    $json = json_encode($Array);
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = null;
    $json_length = strlen($json);

    for ($i = 0; $i < $json_length; $i++) {
        $char = $json[$i];
        $new_line_level = null;
        $post = '';
        if ($ends_line_level !== null) {
            $new_line_level = $ends_line_level;
            $ends_line_level = null;
        }
        if ($in_escape) {
            $in_escape = false;
        } elseif ($char === '"') {
            $in_quotes = !$in_quotes;
        } elseif (!$in_quotes) {
            switch ($char) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = null;
                    $new_line_level = $level;
                    break;
                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;
                case ':':
                    $post = ' ';
                    break;
                case ' ': case "\t": case "\n": case "\r":
                    $char = '';
                    $ends_line_level = $new_line_level;
                    $new_line_level = null;
                    break;
            }
        } elseif ($char === '\\') {
            $in_escape = true;
        }
        if ($new_line_level !== null) {
            $result .= "\r\n".str_repeat("\t", $new_line_level);
        }
        $result .= $char.$post;
    }
    echo $result;
}

function debug($data)
{
    echo '<script>';
    $output = explode("\n", print_r($data, true));
    $out = '';
    foreach ($output as $line) {
        if (trim($line)) {
            $line = addslashes($line);
            $out .= $line;
            echo "console.log(\"{$line}\");";
        }
    }
//    echo "console.log($out);</script>";
    echo '</script>';
}

function caps_keys($Array, $make_lower = false, $include_children = false)
{
    if (is_array($Array)) {
        $cap_keys = [];
        foreach ($Array as $k => $v) {
            if ($include_children === true) {
                if (is_array($v)) {
                    if ($make_lower === true) {
                        $cap_keys[strtolower($k)] = caps_keys($v, $make_lower, $include_children);
                    } else {
                        $cap_keys[strtoupper($k)] = caps_keys($v, $make_lower, $include_children);
                    }
                    unset($Array[$k]);
                } else {
                    if ($make_lower === true) {
                        $cap_keys[strtolower($k)] = $v;
                    } else {
                        $cap_keys[strtoupper($k)] = $v;
                    }
                    unset($Array[$k]);
                }
            } else {
                if ($make_lower === true) {
                    $cap_keys[strtolower($k)] = $v;
                } else {
                    $cap_keys[strtoupper($k)] = $v;
                }
                unset($Array[$k]);
            }
        }
        $Array = $cap_keys;
    }

    return $Array;
}

function caps_vals($Array, $make_lower = false, $include_children = false)
{
    if (is_array($Array)) {
        $cap_vals = [];
        foreach ($Array as $k => $v) {
            if ($include_children === true) {
                if (is_array($v)) {
                    $cap_vals[$k] = caps_vals($v, $make_lower, $include_children);
                    unset($Array[$k]);
                } else {
                    if ($make_lower === true) {
                        $cap_vals[$k] = strtolower($v);
                    } else {
                        $cap_vals[$k] = strtoupper($v);
                    }
                    unset($Array[$k]);
                }
            } else {
                if ($make_lower === true) {
                    $cap_vals[$k] = strtolower($v);
                } else {
                    $cap_vals[$k] = strtoupper($v);
                }
                unset($Array[$k]);
            }
        }
        $Array = $cap_vals;
    }

    return $Array;
}

function funcs($opts /* internal(T,F), natural(T,F), sort(T,F), return(T,F) */ = [])
{
    if (!empty($opts)) {
        $opts = caps_keys($opts, true);
    }
    $funcs = get_defined_functions();
    if (isset($opts['internal']) && $opts['internal'] === true) {
        $funcs = $funcs['internal'];
    } else {
        $funcs = $funcs['user'];
    }
    if (isset($opts['natural']) && $opts['natural'] === true) {
        foreach ($funcs as $k => $v) {
            $funcs[$k + 1] = $v;
        }
        unset($funcs[0]);
    }
    if (isset($opts['sort']) && $opts['sort'] === true) {
        asort($funcs);
    }
    if (isset($opts['return']) && $opts['return'] === true) {
        return $funcs;
    }
    pr($funcs);
}

function make_array($obj)
{
    if (is_object($obj)) {
        $obj = (array) $obj;
        foreach ($obj as $key => $obj_child) {
            $obj_child = make_array($obj_child);
            $obj[$key] = $obj_child;
        }
    } elseif (is_array($obj)) {
        foreach ($obj as $key => $obj_child) {
            $obj_child = make_array($obj_child);
            $obj[$key] = $obj_child;
        }
    }

    return $obj;
}

function make_object($array)
{
    if (is_array($array)) {
        foreach ($array as $key => $array_child) {
            $array[$key] = make_object($array_child);
        }
        $array = (object) $array;
    } elseif (is_object($array)) {
        $temp_array = (array) $array;
        $array = make_object($temp_array);
    }

    return $array;
}

function my_substr($string, $length = 10)
{
    if (strlen($string) > $length) {
        $string = substr($string, 0, $length).'...';
    }

    return $string;
}

function getRealIp()
{
    $ip = '0.0.0.0';
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');
    } elseif (getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    } elseif (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    }

    return $ip;
}

function get_protocol()
{
    if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL']) {
        $proto = $_SERVER['SERVER_PROTOCOL'];
        $protos = explode('/', $proto);
        $protocol = strtolower($protos[0]);
    } else {
        $protocol = '';
    }

    return $protocol;
}

function ajax(array $opts /* TYPE,FILTER,DATA */ = null)
{
    $url_prefix = '';
    if (isset($opts['PREFIX']) && is_string($opts['PREFIX'])) {
        $url_prefix = $opts['PREFIX'];
    }
    $ch = curl_init();
    $vars_string = '';
    if (isset($opts['DATA']) && is_array($opts['DATA'])) {
        $data = array_filter($opts['DATA']);
        foreach ($data as $var => $val) {
            $vars_string .= $var.'='.$val.'&';
        }
        $vars_string = rtrim($vars_string, '&');
    }
    curl_setopt($ch, CURLOPT_URL, $url_prefix.($vars_string ? '?'.$vars_string : ''));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
    $response = curl_exec($ch);
    curl_close($ch);
    if (isset($opts['TYPE']) && $opts['TYPE']) {
        $type = strtolower($opts['TYPE']);
        $obj_types = ['obj', 'object', '->'];
        $array_types = ['arr', 'array', '=>', '[]'];
        if (in_array($type, $obj_types)) {
            $response = json_decode($response);
            if (isset($opts['FILTER']) && $opts['FILTER'] === true) {
                $response = make_array($response);
                $response = array_filter($response);
                $response = make_object($response);
            }
        } elseif (in_array($type, $array_types)) {
            $response = json_decode($response);
            $response = make_array($response);
            if (isset($opts['FILTER']) && $opts['FILTER'] === true) {
                $response = array_filter($response);
            }
        }
    } else {
        if (isset($opts['FILTER']) && $opts['FILTER'] === true) {
            $response = json_decode($response);
            $response = make_array($response);
            $response = array_filter($response);
            $response = json_encode($response);
        }
    }

    return $response;
}

function clean($str)
{
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    $str = strip_tags($str);

    return $str;
}

function bulk_clean($Array, $clean_contents = false)
{
    if (is_array($Array) && !empty($Array)) {
        foreach ($Array as $k => $v) {
            if ($clean_contents === true) {
                if (is_array($v)) {
                    $v = bulk_clean($v, true);
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

function get_needle($haystack, $needle, $after_needle = false)
{
    if ($after_needle === true) {
        return clean(substr($haystack, (strpos($haystack, $needle) - strlen($haystack))));
    }

    return clean(substr($haystack, 0, strpos($haystack, $needle)));
}

function is_valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function bulk_define(array $defines)
{
    if (is_array($defines)) {
        foreach ($defines as $name => $value) {
            define($name, $value);
        }
    }
}

function file_get_ext($file)
{
    $ext = explode('.', $file);

    return end($ext);
}

function get_vars(&$str)
{
    $vars = [];
    if (is_string($str)) {
        $vars = explode('/', $str);
        foreach ($vars as $k => $v) {
            $vars[$k] = clean($v);
        }
        $vars = array_filter($vars);
        $c = count($vars);
        while ($c) {
            $vars[$c] = $vars[$c - 1];
            $c--;
        }
        unset($vars[0]);
    }
    $str = $vars;
}

function session_exists()
{
    return isset($_SESSION['img_editor_user_id']) && isset($_SESSION['img_editor_fb_id']);
}

function add_session($uid, $fb_id)
{
    $_SESSION['img_editor_user_id'] = $uid;
    $_SESSION['img_editor_fb_id'] = $fb_id;

    return 1;
}

function remove_ext($Path)
{
    $a = explode('.', $Path);
    count($a) > 1 ? array_pop($a) : $a;

    return implode('.', $a);
}

function make_path($Path)
{
    if ($Path) {
        $Path = clean($Path);
        $Path = ltrim($Path, '/');
        $Path = remove_ext($Path);
        $Path .= '.php';
    }

    return $Path;
}

function get_tables($database, $all_info = false)
{
    $tables = [];
    $query = 'SHOW TABLE STATUS';
    $get_tables = $database->query($query)->fetchAll(PDO::FETCH_ASSOC);
    if ($all_info == true) {
        return $get_tables;
    }
    foreach ($get_tables as $table) {
        $tables[] = $table['Name'];
    }

    return $tables;
}

function get_table_info($database, $table_name)
{
    $info = [];
    $all = get_tables($database, true);
    foreach ($all as $info) {
        if ($info['Name'] == $table_name) {
            return $info;
        }
    }
}

function get_structure($database, $table_name)
{
    $get_structure = $database->query('SHOW CREATE TABLE '.$table_name)->fetchAll(PDO::FETCH_ASSOC);
    $structure = $get_structure[0];
    $structure['Create Table'] = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $structure['Create Table']);
    $structure['Create Table'] = str_replace('"', '`', $structure['Create Table']);
    $structure['Create Table'] .= ' ;';

    return $structure['Create Table'];
}

function get_data($database, $table_name)
{
    $get_data = $database->select($table_name, '*');

    return $get_data;
}

function get_folder_data($folder, $opts/* remove_dots(true,false), return(dir,file,.txt,.mp3) */ = [])
{
    $folder = rtrim($folder).'/';
    if (isset($opts) && is_array($opts) && !empty($opts)) {
        //make all options to lowercase
        $opts = array_change_key_case($opts);
        $opts = array_map('strtolower', $opts);
    }
    $get_contents = @scandir($folder);
    if (is_array($get_contents)) {
        if (isset($opts['remove_dots']) && $opts['remove_dots'] == true) {
            $get_contents = array_diff($get_contents, ['.', '..']);
        }
        $contents = [];
        foreach ($get_contents as $content) {
            $filename = $folder.$content;
            if (isset($opts['return']) && $opts['return']) {
                if (substr($opts['return'], 0, 1) === '.') {
                    $get_ext = '.'.file_get_ext($content);
                    if ($get_ext === $opts['return']) {
                        $contents[] = $content;
                    }
                } else {
                    if ($opts['return'] == 'dir' && is_dir($filename)) {
                        $contents[] = $content;
                    }
                    if ($opts['return'] == 'file' && is_file($filename)) {
                        $contents[] = $content;
                    }
                }
            } else {
                $contents[] = $content;
            }
        }

        return $contents;
    }

    return [];
}

function dirToArray($dir)
{
    $result = [];
    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, ['.', '..'])) {
            if (is_dir($dir.DIRECTORY_SEPARATOR.$value)) {
                $result[$value] = dirToArray($dir.DIRECTORY_SEPARATOR.$value);
            } else {
                $result[] = $value;
            }
        }
    }

    return $result;
}

function delTree($dir)
{
    rtrim($dir, '/');
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }

    return rmdir($dir);
}

function footer()
{
    echo '<div id="footer"><div class="container"><p class="text-muted">Designed &amp; Developed by <a href="https://www.facebook.com/pritpalsingh.in" target="_blank">Pritpal Singh</a><span class="pull-right hidden-xs">Designed Using <a href="http://getbootstrap.com" target="_blank">Bootstrap</a></span></p></div></div>';
}

function get_saved_tables($structures)
{
    $tables = [];
    foreach ($structures as $table) {
        $tables[] = remove_ext($table);
    }

    return $tables;
}

function get_pk($database, $table_name, $check_AI = false)
{
    $query = 'SHOW FIELDS FROM `'.$table_name."` WHERE `Key` = 'PRI'";
    $get = $database->query($query)->fetchAll(PDO::FETCH_ASSOC);
    if (isset($get[0]) && is_array($get[0])) {
        if ($check_AI === true) {
            return $get[0]['Extra'] === 'auto_increment' ? true : false;
        }

        return $get[0]['Field'];
    }
}

function empty_table($database, $table_name)
{
    return $database->query('TRUNCATE TABLE `'.$table_name.'`');
}

function drop_table($database, $table_name)
{
    return $database->query('DROP TABLE `'.$table_name.'`');
}

function get_cols($database, $table_name, $skip_pk = false)
{
    $fields = [];
    $query = 'SHOW COLUMNS FROM `'.$table_name.'`';
    $get = $database->query($query)->fetchAll();
    foreach ($get as $data) {
        if ($skip_pk === true) {
            if (!is_pk($database, $table_name, $data['Field'])) {
                $fields[] = $data['Field'];
            }
        } else {
            $fields[] = $data['Field'];
        }
    }

    return $fields;
}

function alert($value)
{
    echo '<script>alert(\''.$value.'\');</script>';
}

function is_pk($database, $table_name, $col)
{
    $pk = get_pk($database, $table_name);

    return $col == $pk;
}

function has_only_pk($database, $table_name)
{
    $cols = get_cols($database, $table_name);
    if (count($cols) == 1) {
        if (is_pk($database, $table_name, $cols[0])) {
            return true;
        }
    }

    return false;
}

function has_pk($database, $table_name)
{
    $cols = get_cols($database, $table_name);
    foreach ($cols as $col) {
        if (is_pk($database, $table_name, $col)) {
            return true;
        }
    }

    return false;
}

function is_col($database, $table_name, $col)
{
    $cols = get_cols($database, $table_name);

    return in_array($col, $cols);
}

function get_table_config($table_name = null)
{
    $table_config = [];
    $filename = root.'include/table_config.php';
    if (file_exists($filename)) {
        $table_config = unserialize(file_get_contents($filename));
    }
    if (isset($table_name) && is_string($table_name)) {
        if (array_key_exists($table_name, $table_config)) {
            return $table_config[$table_name];
        }

        return [];
    }

    return $table_config;
}

function set_table_config($table_config, $only_non_exist = false)
{
    $filename = root.'include/table_config.php';
    $err_trgr = false;
    if ($only_non_exist === true) {
        if (!file_exists($filename)) {
            $err_trgr = false;
        } else {
            $err_trgr = true;
        }
    }
    if ($err_trgr === false) {
        $handle = fopen($filename, 'w');
        if ($handle) {
            $data = serialize($table_config);
            fwrite($handle, $data);
            fclose($handle);

            return true;
        }
    }
}

function download_headers($type = 'text/plain', $filename = 'download.txt')
{
    header('Content-Type: '.$type);
    header('Content-Disposition: attachement; filename="'.$filename.'"');
}

function is_table($database, $table_name)
{
    $tables = get_tables($database);

    return in_array($table_name, $tables);
}
