<?php

set_time_limit(0);
if (isset($_GET['action'])) {
    extract($_GET);
    if ($action == 'get_backup_dates') {
        $folder_content = get_folder_data(root . 'backups/', array('remove_dots' => TRUE, 'return' => 'dir'));
        rsort($folder_content);
        pj($folder_content);
    }
    if ($action == 'get_date_backups') {
        $folder = root . 'backups/' . $date;
        $folder_content = get_folder_data($folder, array('remove_dots' => TRUE, 'return' => 'dir'));
        rsort($folder_content);
        pj($folder_content);
    }
    if ($action == 'get') {
        $query = "show table status";
        $tables_status = $database->query($query)->fetchAll(PDO::FETCH_ASSOC);
        ppj($tables_status[0]);
//        pj($tables_status, TRUE);
    }
    if ($action == 'get_tables') {
        $query = "show table status";
        $tables_status = $database->query($query)->fetchAll(PDO::FETCH_ASSOC);
        pj($tables_status);
    }
    if ($action == 'download') {
        $type = 'application/octet-stream';
        $filename = database_name . '_' . $table_name . '_' . date('d-M-Y_H-i-s') . '.csv';
        download_headers($type, $filename);
        $output = '';
        $all_data = $database->select($table_name, '*');
        $cols = get_cols($database, $table_name);
//        $output.= implode(';', $cols) . "\n";
        foreach ($all_data as $data) {
            $output.= implode(';', $data) . "\n";
        }
        echo trim($output);
    }
} elseif (isset($_POST['action'])) {
    extract($_POST);
    if ($action == 'del_db_config') {
        $err = FALSE;
        $backups_folder = root . 'backups/';
        $all_backups = get_folder_data($backups_folder, array('remove_dots' => TRUE, 'return' => 'dir'));
        foreach ($all_backups as $date) {
            $dir = $backups_folder . $date;
            if (!delTree($dir))
                $err = TRUE;
        }
        if ($err === FALSE) {
            $filename = root . 'include/db_defines.php';
            if (@unlink($filename)) {
                $filename = root . 'include/table_config.php';
                if (@unlink($filename)) {
                    session_destroy();
                    echo 1;
                } else
                    echo 0;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }
    if ($action == 'backup_now') {
        $err_trgr = FALSE;
        $backups_folder = root . 'backups/';
        $date_folder_name = $backups_folder . date('d-M-Y') . '/';
        if (!is_dir($date_folder_name)) {
            if (!@mkdir($date_folder_name, 0777, true))
                $err_trgr = TRUE;
        }
        if ($err_trgr === FALSE) {
            $backup_folder_name = $date_folder_name . 'backup_' . date('H:i:s') . '/';
            if (!is_dir($backup_folder_name)) {
                if (!@mkdir($backup_folder_name, 0777, true))
                    $err_trgr = TRUE;
            }
            if ($err_trgr === FALSE) {
                //make backup here
                $tables = get_tables($database);
                $structures = '';
                $structures2 = array();
                foreach ($tables as $table_name) {
                    $filename = $backup_folder_name . $table_name . '.tbld';
                    $handle = @fopen($filename, 'w');
                    if ($handle) {
                        $data = get_data($database, $table_name);
                        fwrite($handle, serialize($data));
                        @fclose($handle);
                    }
                    $filename = $backup_folder_name . $table_name . '.tbls';
                    $handle = @fopen($filename, 'w');
                    if ($handle) {
                        $structure = get_structure($database, $table_name);
                        fwrite($handle, serialize($structure));
                        @fclose($handle);
                    }
                }
            }
        }
        if ($err_trgr === FALSE) {
            echo 1;
        } else {
            echo 0;
        }
    }
    if ($action == 'restore_backup') {
        $err_trgr = FALSE;
        $backup_folder = root . 'backups/' . $date . '/' . $backup . '/';
        //get table structures
        $structures = get_folder_data($backup_folder, array('return' => '.tbls'));

        $saved_tables = get_saved_tables($structures);
        $available_tables = get_tables($database);

        foreach ($available_tables as $table_name) {
            if (!drop_table($database, $table_name))
                $err_trgr = TRUE;
        }

        if ($err_trgr === FALSE) {
            foreach ($saved_tables as $table_name) {
                $tbl_str = unserialize(file_get_contents($backup_folder . $table_name . '.tbls'));
                $database->query($tbl_str);
                $tbl_data = unserialize(file_get_contents($backup_folder . $table_name . '.tbld'));
                $cols = get_cols($database, $table_name);
                foreach ($tbl_data as $data) {
                    $database->insert($table_name, $data);
                }
            }
        }

        if ($err_trgr === FALSE) {
            echo 1;
        } else {
            echo 0;
        }
    }
    if ($action == 'delete_backups') {
        $del_folder = root . 'backups/' . $date;
        if (delTree($del_folder)) {
            echo 1;
        } else {
            echo 0;
        }
    }
    if ($action == 'delete_backup') {
        $del_folder = root . 'backups/' . $date . '/' . $backup;
        if (delTree($del_folder)) {
            echo 1;
        } else {
            echo 0;
        }
    }
    if ($action == 'save_table_config') {
        $table_config = get_table_config();
        $err_trgr = FALSE;
        if (!empty($cols)) {
            foreach ($cols as $col) {
                if (is_pk($database, $table_name, $col) || !is_col($database, $table_name, $col))
                    $err_trgr = TRUE;
            }
        } else {
            $cols = array();
        }
        if ($err_trgr === FALSE) {
            $table_config[$table_name] = $cols;
            if (set_table_config($table_config))
                echo 1;
            else
                echo 0;
        } else
            echo 0;
    }
    if ($action == 'delete_table') {
        $query = 'DROP TABLE `' . $table_name . '`';
        if ($database->query($query))
            echo 1;
        else
            echo 0;
    }
}    
