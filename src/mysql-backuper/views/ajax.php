<?php

set_time_limit(0);
if (isset($_GET['action'])) {
    extract($_GET);
    if ($action == 'get_backup_dates') {
        $folder_content = get_folder_data(root.'backups/', ['remove_dots' => true, 'return' => 'dir']);
        rsort($folder_content);
        echo json_encode($folder_content);
    }
    if ($action == 'get_date_backups') {
        $folder = root.'backups/'.$date;
        $folder_content = get_folder_data($folder, ['remove_dots' => true, 'return' => 'dir']);
        rsort($folder_content);
        echo json_encode($folder_content);
    }
} elseif (isset($_POST['action'])) {
    extract($_POST);
    if ($action == 'del_db_config') {
        $err = false;
        $backups_folder = root.'backups/';
        $all_backups = get_folder_data($backups_folder, ['remove_dots' => true, 'return' => 'dir']);
        foreach ($all_backups as $date) {
            $dir = $backups_folder.$date;
            if (!delTree($dir)) {
                $err = true;
            }
        }
        if ($err === false) {
            $filename = root.'include/db_defines.php';
            if (@unlink($filename)) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }
    if ($action == 'backup_now') {
        $err_trgr = false;
        $backups_folder = root.'backups/';
        $date_folder_name = $backups_folder.date('d-M-Y').'/';
        if (!is_dir($date_folder_name)) {
            if (!@mkdir($date_folder_name, 0777, true)) {
                $err_trgr = true;
            }
        }
        if ($err_trgr === false) {
            $backup_folder_name = $date_folder_name.'backup_'.date('H:i:s').'/';
            if (!is_dir($backup_folder_name)) {
                if (!@mkdir($backup_folder_name, 0777, true)) {
                    $err_trgr = true;
                }
            }
            if ($err_trgr === false) {
                //make backup here
                $tables = get_tables($database);
                $structures = '';
                $structures2 = [];
                foreach ($tables as $table_name) {
                    $filename = $backup_folder_name.$table_name.'.tbld';
                    $handle = @fopen($filename, 'w');
                    if ($handle) {
                        $data = get_data($database, $table_name);
                        fwrite($handle, serialize($data));
                    }
                    @fclose($handle);
                    $filename = $backup_folder_name.$table_name.'.tbls';
                    $handle = @fopen($filename, 'w');
                    if ($handle) {
                        $structure = get_structure($database, $table_name);
                        fwrite($handle, serialize($structure));
                    }
                    @fclose($handle);
                }
            }
        }
        if ($err_trgr === false) {
            echo 1;
        } else {
            echo 0;
        }
    }
    if ($action == 'restore_backup') {
        $err_trgr = false;
        $backup_folder = root.'backups/'.$date.'/'.$backup.'/';
        //get table structures
        $structures = get_folder_data($backup_folder, ['return' => '.tbls']);

        $saved_tables = get_saved_tables($structures);
        $available_tables = get_tables($database);

        foreach ($available_tables as $table_name) {
            if (!drop_table($database, $table_name)) {
                $err_trgr = true;
            }
        }

        if ($err_trgr === false) {
            foreach ($saved_tables as $table_name) {
                $tbl_str = unserialize(file_get_contents($backup_folder.$table_name.'.tbls'));
                $database->query($tbl_str);
                $tbl_data = unserialize(file_get_contents($backup_folder.$table_name.'.tbld'));
                $cols = get_cols($database, $table_name);
                foreach ($tbl_data as $data) {
                    $database->insert($table_name, $data);
                }
            }
        }

        if ($err_trgr === false) {
            echo 1;
        } else {
            echo 0;
        }
    }
    if ($action == 'delete_backups') {
        $del_folder = root.'backups/'.$date;
        if (delTree($del_folder)) {
            echo 1;
        } else {
            echo 0;
        }
    }
    if ($action == 'delete_backup') {
        $del_folder = root.'backups/'.$date.'/'.$backup;
        if (delTree($del_folder)) {
            echo 1;
        } else {
            echo 0;
        }
    }
}
