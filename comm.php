<?php
    include 'config.php';
    function updProduct($key, $databases) {
        intval($key);
        if(array_key_exists($key, $databases)) {
            return $databases[$key]['db'];
        }
        else {
            return "Database configuration not found.";
        }
    }

    if (isset($_GET['action']) && $_GET['action'] === 'click') {
        echo updProduct($_GET['type'], $databases);  
    }