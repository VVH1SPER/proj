<?php

    include 'config.php';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    function sendData($sql, $databases) {
        $connections = [];
        $file = 'backup.json';        

        try {
            if(file_exists($file)) {
                $json = file_get_contents($file);
                $backup_data = json_decode($json, true);
                if(!is_array($backup_data)) {
                    $backup_data = [];
                }
            }
            else {
                $backup_data = [];
            }
        }
        catch(ErrorException $e) {
            return 'Error occured while processing file existance check';
        }
        

        foreach($databases as $db) {
            try {

                $conn = mysqli_init();
                mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 2);

                if(mysqli_real_connect($conn, $db['host'], $db['user'], $db['pass'], $db['db'])){
                    $connections[$db['db']] = $conn;
                }
                else {
                    $connections[$db['db']] = null;
                    $new_data = [['db' => $db['db'], 'sql' => $sql]];

                    $backup_data = array_merge($backup_data, $new_data); 
                }
            }
            catch(Exception $e) {
                $connections[$db['db']] = null;
                $new_data = [['db' => $db['db'], 'sql' => $sql]];

                //echo $new_data['db'] . '+</br>';

                $backup_data = array_merge($backup_data, $new_data); 
            }
        }
        file_put_contents($file, json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return 'Done';
    }

    if (isset($_POST['action']) && $_POST['action'] === 'upd') {
        
        $sql = $_POST['sql'];
        //echo $sql;
        //return;
        echo sendData($sql, $databases);
    }