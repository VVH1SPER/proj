<?php

    $myIp     = "25.30.27.136"; // ← твой IP
    $myPort   = 9000;

    $uid = 'PC1';

    // --- список собеседников (все узлы в сети) ---
    $peers = [
        ["ip" => "25.7.31.48", "port" => 9000],
        //["ip" => "192.168.1.12", "port" => 9000],
        // добавь сюда остальных участников
    ];

    // --- создаём сервер для приёма сообщений ---
    $server = stream_socket_server("tcp://$myIp:$myPort", $errno, $errstr);
    if (!$server) {
        die("Ошибка сервера: $errstr ($errno)\n");
    }
    stream_set_blocking($server, false);

    echo "✅ Групповой чат запущен! Слушаю $myIp:$myPort\n";
    echo "Участники: " . implode(", ", array_column($peers, "ip")) . "\n";
    echo "> ";

    while (true) {
        $read = [$server, STDIN];
        $write = null;
        $except = null;

        if (stream_select($read, $write, $except, null) > 0) {
            foreach ($read as $r) {
                // --- приём сообщений ---
                if ($r === $server) {
                    $conn = stream_socket_accept($server, 0);
                    if ($conn) {
                        $msg = fread($conn, 1024);
                        echo "\n$msg\n> ";
                        fclose($conn);
                    }
                }
                // --- отправка сообщений ---
                elseif ($r === STDIN) {
                    $msg = trim(fgets(STDIN));
                    if ($msg === "exit") {
                        echo "👋 Выход из чата...\n";
                        exit;
                    }

                    $msg = $uid . ': ' . $msg;

                    foreach ($peers as $peer) {
                        $client = @stream_socket_client("tcp://{$peer['ip']}:{$peer['port']}", $errno, $errstr, 1);
                        if ($client) {
                            fwrite($client, $msg);
                            fclose($client);
                        } else {
                            echo "⚠️ Не удалось подключиться к {$peer['ip']}:{$peer['port']}\n";
                        }
                    }
                    //echo "📤 [Вы]: $msg\n> ";
                }
            }
        }
    }

?>