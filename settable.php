<?php
    include 'config.php';

    if (isset($_POST['action']) && $_POST['action'] === 'toset') {
        
        //echo "<script>console.log('POST Data:', " . json_encode($_POST) . ");</script>";
        $main = $_POST['value'] ?? null;

        $conn = mysqli_init();
        mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 2);

    
        $db = $root;
        if(mysqli_real_connect($conn, $db['host'], $db['user'], $db['pass'], $db['db'])) {
            //echo "Connection to " . $db['db'] . " successful.";
        }
        else {
            echo "Connection to " . $db['db'] . " failed.";
            exit;
        }

        $q = "SELECT id, name, quantity, price, user_id FROM products";

        $resData = mysqli_query($conn, $q);
        $numRows = mysqli_num_rows($resData);

        if ($numRows === 0) {
            echo "<div class='empty'>Таблица пуста.</div>";
            mysqli_free_result($resData);
        }

        echo "<table>";
            echo "<thead><tr>";
                echo "<th>name</th>";
                echo "<th>quantity</th>";
                echo "<th>price</th>";
                echo "<th>status</th>";
                echo "<th>action</th>";
            echo "</tr></thead>";
            echo "<tbody>";

        mysqli_data_seek($resData, 0);
        while ($r = mysqli_fetch_assoc($resData)) {

            $s = json_encode($r, true);
            $s = json_decode($s, true);
            $mid = $s['user_id'];

            $color = ($mid == $main) ? "green" : "red";
            $icon = ($mid == $main) ? "🟢 active" : "🔴 inactive";

            $editable = "";
            $dis = "disabled ";
            
            if($mid == $main) {
                $editable = "editable ";
                $dis = "";    
            }
            echo "<tr>";
            foreach ($r as $key => $val) {
                if (is_null($val)) {
                    echo "<td class='empty'>NULL</td>";
                } else {

                    switch($key) {
                        case 'id':
                            $id = $val;
                            break;
                        case 'name':
                            echo "<td id='" . $id . "' class='" . $editable . "name'>" . htmlspecialchars($val) . "</td>";
                            break;
                        case 'quantity':
                            echo "<td class='" . $editable . "quant'>" . htmlspecialchars($val) . "</td>";
                            break;
                        case 'price':
                            echo "<td class='" . $editable . "price'>" . htmlspecialchars($val) . "</td>";
                            break;
                        case 'user_id':
                            echo "<td style='color:" . $color . ";' class='uid" . htmlspecialchars($val) . "'>" . $icon . "</td>";
                            break;
                    }                  
                }
            }
            echo "<td><button " . $dis . "class='edit-btn'>Edit</button></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    
        mysqli_free_result($resData);
        mysqli_close($conn);
    }
