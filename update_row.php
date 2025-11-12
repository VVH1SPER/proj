<?php
    require 'config.php';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT | MYSQLI_REPORT_OFF);

    header('Content-Type: application/json; charset=utf-8');    

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Invalid method']);
        exit;
    }

    $id       = $_POST['id'] ?? null;
    $name     = $_POST['name'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $price    = $_POST['price'] ?? null;

    if (!$id || $name === null || $quantity === null || $price === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing fields']);
        exit;
    }

    // SQL для обновления
    $sql = sprintf(
        "UPDATE products SET name='%s', quantity=%d, price=%.2f WHERE id=%d",
        addslashes($name),
        (int)$quantity,
        (float)$price,
        (int)$id
    );

    $results = [];

    $conn = mysqli_init();
    mysqli_real_connect($conn, $root['host'], $root['user'], $root['pass'], $root['db']);
    $q = "SELECT * FROM users";
    $resData = mysqli_query($conn, $q);
    
    mysqli_data_seek($resData, 0);
    
    while ($r = mysqli_fetch_assoc($resData)) {

        $conn = mysqli_init();
        //echo "User ID: " . $r['id'] . " - Status: active</br>";
        try {
            //mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 2);
            if(mysqli_real_connect($conn, $r['ip'], $r['login'], $r['pass'], $r['db'])){
                //echo json_encode("Connected to " . $r['db']);
                //return;
                $startTime = microtime(true);
                if (@mysqli_query($conn, $sql)) {
                    //echo json_encode("Updated " . $r['db']);
                    //return;
                    $results[$r['id']] = ['status' => 'ok', 'time' => round((microtime(true)-$startTime)*1000, 1)];
                }
                else {
                    //echo json_encode("Failed to update " . $r['db'] . ": " . mysqli_error($conn));
                    //return;
                    $results[$r['id']] = ['status' => 'fail_query', 'error' => mysqli_error($conn)];
                    //log_failed_query($sql, $r['id'], 'query error');
                }
            }
            else {
                $results[$r['id']] = ['status' => 'fail_connect'];
                //log_failed_query($sql, $r['id'], 'connection failed');
            }     
        }  
        catch (Exception $e) {
            $results[$r['id']] = ['status' => 'exception', 'error' => $e->getMessage()];
            //log_failed_query($sql, $r['id'], 'exception');
        }

        @mysqli_close($conn);
    }

    // возвращаем JSON отчёт
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


    // === ЛОГИРОВАНИЕ НЕУДАЧ ===
    function log_failed_query($sql, $server, $reason) {
        require 'config.php';

        $conn = mysqli_init();
        mysqli_real_connect($conn, $root['host'], $root['user'], $root['pass'], $root['db']);

        if (!$conn) return;

        $sq = sprintf(
            "INSERT INTO log (server_name, query_text, reason, created_at)  VALUES ('%d', '%s', '%s', NOW())",
                (int)$server,
                (string)$sql,
                (string)$reason
            );

        if (@mysqli_query($conn, $sql)) {
            echo json_encode("Updated log table on " . $root['host'] . "\n");
        } else {
            echo json_encode("Failed to update log table on " . $root['host'] . ": " . mysqli_error($conn));
        }
        mysqli_close($conn);
    }