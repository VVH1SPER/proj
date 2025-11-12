<?php

    include 'config.php';
    function syncAll($databases) {
        $file = 'backup.json';
        try {
            if(file_exists($file)) {
                $json = file_get_contents($file);
                $backup_data = json_decode($json, true);
                if(!is_array($backup_data)) {
                    return 'No sync needed';
                }

                foreach($backup_data as $bd) {
                    $index = array_search($bd['db'], array_column($databases, 'db'));
                    if($index !== false) {
                        $selected = $databases[$index];
                        echo "Host " . $selected['host'] . " was found</br>";
                    }
                    else {
                        echo "No host on " . $bd['db'] . " was found";
                    }
                }
            }
            else {
                return 'No sync needed';
            }
        }
        catch(Exception $e) {
            $err = "Error occured: " . $e;
            return $err;
        }

    }

    if(isset($_POST['action']) && $_POST['action'] === "sync") {
        echo 'Passed';
        echo syncAll($databases);
    }

    syncAll($databases);