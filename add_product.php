<?php
    if($_POST['action'] === 'add_product') {
        include 'config.php';

        $name = $_POST['name'] ?? '';
        $quantity = $_POST['quantity'] ?? 0;
        $price = $_POST['price'] ?? 0.0;


        return $name;


        $conn = mysqli_init();
        mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 2);

        $db = $root;
        if(mysqli_real_connect($conn, $db['host'], $db['user'], $db['pass'], $db['db'])) {
            // Connection successful
        }
        else {
            echo "Connection to " . $db['db'] . " failed.";
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO products (name, quantity, price) VALUES (?, ?, ?)");
        $stmt->bind_param("sid", $name, $quantity, $price);

        if ($stmt->execute()) {
            echo "Product added successfully.";
        } else {
            echo "Error adding product: " . $stmt->error;
        }

        $stmt->close();
        mysqli_close($conn);
    }