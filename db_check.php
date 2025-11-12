<?php

    include 'config.php';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT | MYSQLI_REPORT_OFF);

    $mysqli = mysqli_init();

    $conn_cou = sizeof($databases);
    
    $connections = [];

    try {
        $conn = mysqli_init();
        mysqli_real_connect($conn, $root['host'], $root['user'], $root['pass'], $root['db']);
        $q = "SELECT * FROM users";
        $resData = mysqli_query($conn, $q);
        
        mysqli_data_seek($resData, 0);
        
        while ($r = mysqli_fetch_assoc($resData)) {

            //echo "<script>console.log('User Record:', " . json_encode($r) . ");</script>";
            $conn = mysqli_init();
            //echo "User ID: " . $r['id'] . " - Status: active</br>";
            try {
                mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 2);
                if(mysqli_real_connect($conn, $r['ip'], $r['login'], $r['pass'], $r['db'])){
                    $connections[$r['login']] = [1, $r['user_id']];
                }
                else {
                    $connections[$r['login']] = [0, $r['user_id']];
                }     
            }  
            catch (Exception $e) {
                $connections[$r['login']] = [0, $r['user_id']];
            }
        }

        echo json_encode($connections);

    } catch (Throwable $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
   
