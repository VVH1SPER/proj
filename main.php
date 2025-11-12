<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="jquery.js"></script>
    <title>Database</title>
</head>
<body>
    
    <div id="data">Establishing connection...</div>
    <div id="dbase"></div> 
    <input type="text" id="input" placeholder="Type something...">
    <button id="click">Click</button>
    <button id="upd">Update</button>
    <div id="response_upd"></div>

    <button id="sync">Sync all</button>
    <div id="syncres"></div>

    <script>

        $(document).ready(function() {
            $('#click').click(function() {
                const $btn = $(this);
                $btn.prop('disabled', true);
                
                $.ajax({
                    url: 'comm.php',
                    method: 'GET',
                    data: { 
                        action: 'click',
                        type: $('#input').val(),
                        _t: new Date().getTime()
                    },
                    success: function(data) {
                        if (data.trim() !== "") {
                            $('#dbase').html(data);
                        } else {
                            console.log("Пустой ответ от сервера");
                        }
                        $btn.prop('disabled', false);
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            });

            $('#upd').click(function(){

                $btn2 = $(this);
                $btn2.prop('disabled', true);
                $.ajax({
                    url: 'upd_all.php',
                    method: 'POST',
                    data: { 
                        action: 'upd',
                        sql: 'SELECT * from dsd'
                    },
                    success: function(data) {
                        if (data.trim() !== "") {
                            $('#response_upd').html(data);
                        } else {
                            console.log("Пустой ответ от сервера");
                        }
                        $btn2.prop('disabled', false);
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });

            });

            $('#sync').click(function() {
                const $btn3 = $(this);
                $btn3.prop('disabled', true);
                
                $.ajax({
                    url: 'sync.php',
                    method: 'GET',
                    data: { 
                        action: 'sync',
                        _t: new Date().getTime()
                    },
                    success: function(data) {
                        if (data.trim() !== "") {
                            $('#syncres').html(data);
                        } else {
                            console.log("Пустой ответ от сервера");
                        }
                        $btn3.prop('disabled', false);
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            });
        });


        function checkConnection() {
            fetch('db_check.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('data').innerHTML = data;
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        checkConnection();

        setInterval(checkConnection, 5000); // Проверять каждые 5 секунд


        /*
        document.getElementById('click').addEventListener('click', () => {
            fetch('comm.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('data2').innerHTML = data;
                })
                .catch(error => console.error('Error fetching data:', error));
        });
        */
    </script>

</body>
</html>