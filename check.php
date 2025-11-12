<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <title>Сводка БД</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script type="text/javascript" src="jquery.js"></script>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; background: #f8f9fb; color: #222; }
        .db-block { background: #fff; padding: 16px; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 20px; }
        h1,h2,h3 { margin: 0 0 10px 0; }
        .meta { color: #666; font-size: 0.9em; margin-bottom: 12px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 14px; }
        th, td { border: 1px solid #e2e6ea; padding: 8px 10px; text-align: left; font-size: 14px; }
        th { background: #f1f5f9; font-weight: 600; }
        .empty { color: #888; font-style: italic; }
        .error { color: #a94442; background: #f2dede; padding: 8px; border-radius: 6px; }
        .small { font-size: 0.85em; color: #555; }
        #serv_stat table { 
            width: auto; 
        }
    </style>
    </head>
    <body>
        <h1>Product list</h1>

        <div id="serv_stat">

            <table>
                <thead>
                    <tr>
                        <th>Server</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="stat_body">

                </tbody>
            </table>

        </div>

        <div id="data_table">
        </div>
        
        <script>

            //document.addEventListener('click', function(e) {
            function editHandler(e) {
                if (e.target && e.target.classList.contains('edit-btn')) {
                    const btn = e.target;
                    const row = btn.closest('tr');

                    let id = row.querySelector('td.name').id;

                    const editableCells = row.querySelectorAll('td.editable');

                    if(this.textContent === 'Save') {

                        let changes = [];
                        editableCells.forEach(td => {
                            const input = td.querySelector('input');
                            if(input) {
                                const oldVal = input.defaultValue;
                                const newVal = input.value;
                                td.textContent = input.value;

                                if(newVal !== oldVal) {
                                    changes.push(`${id}: "${oldVal}" → "${newVal}"`);
                                    td.dataset.original = newVal;
                                }
                            }
                        });

                        if(changes.length > 0) {
                            const updatedData = {
                                id: id,
                                name: row.querySelector('.name').textContent.trim(),
                                quantity: row.querySelector('.quant').textContent.trim(),
                                price: row.querySelector('.price').textContent.trim()
                            };

                            console.log(updatedData)
                            
                            $.ajax({
                                url: 'update_row.php',
                                method: 'POST',
                                data: updatedData,
                                success: function(response) {
                                    //console.log("Изменения: \n" + changes.join('\n'));
                                    console.log("RAW update_row.php response:\n", response);
                                },
                                error: function(xhr, status, error) {
                                    console.error('Update error:', error);
                                }
                            });

                            /*
                            fetch('update_row.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: new URLSearchParams(updatedData)
                            })
                            .then(
                                async response => {
                                    console.log("Изменения: \n" + changes.join('\n'));
                                    const text = await response.text();
                                    console.log("RAW update_row.php response:\n", response);
                                }
                                //res => res.json()
                            )
                            .then(resp => {
                                console.log('Update results:', resp);
                                if (resp.logged && resp.logged.length > 0) {
                                    alert(`⚠️ Некоторые базы недоступны.\nЛоги записаны для: ${resp.logged.join(', ')}`);
                                } else {
                                    console.log('✅ Все базы обновлены.');
                                }
                            })
                            .catch(err => console.error('Update error:', err));
                            */

                        } else {
                            console.log("Изменений нет.");
                        }
                        this.textContent = 'Edit';
                    } else {
                        editableCells.forEach(td => {
                            const value = td.textContent;
                            td.innerHTML = `<input type='text' value='${value}'>`;
                        });
                        this.textContent = 'Save';
                    }
                }
            }
            
            let main;

            function checkConnection() {
                fetch('db_check.php')
                    .then(
                        /*
                        async response => {
                            const text = await response.text();
                            console.log("RAW db_check.php response:\n", text);
                            try {
                                const data = JSON.parse(text);
                                handleData(data);
                            } catch (err) {
                                console.error("JSON parse error:", err);
                            }
                        }
                        */
                        response => response.json()
                    )
                    .then(data => {

                        if (data.error) {
                            console.error('DB Check error:', data.error);
                            $('#stat_body').html(
                                `<tr><td colspan="2" style="color:red;">${data.error}</td></tr>`
                            );
                            return;
                        }

                        body = document.getElementById('stat_body');
                        body.innerHTML = '';

                        //console.log(data);

                        Object.entries(data).forEach(([key, status]) => {

                            if(!main && key == 'root') {
                                main = status[1];

                                $.ajax({
                                    url: 'settable.php',
                                    method: 'POST',
                                    data: { 
                                        action: 'toset',
                                        value: main
                                    },
                                    success: function(data) {
                                        console.log("Server response for table data.");
                                        if (data.trim() !== "") {
                                            $('#data_table').html(data);

                                            document.querySelectorAll('.edit-btn').forEach(btn => {
                                                btn.addEventListener('click', editHandler);
                                            });

                                            let dtable = document.getElementById('data_table');
                                            let bt = document.createElement('button');
                                            bt.textContent = 'Add New Product';
                                            bt.setAttribute('id', 'add-btn');
                                            bt.style.marginBottom = '10px';
                                            bt.onclick = function() {
                                                document.getElementById("productModal").style.display = "block";
                                                document.getElementById("modalOverlay").style.display = "block";
                                            };
                                            dtable.appendChild(bt);

                                        } else {
                                            console.log("Empty server response");
                                        }
                                    }
                                });
                            }

                            trb = document.createElement('tr');
                            tdb = document.createElement('td');
                            tdb.textContent = key;
                            trb.appendChild(tdb);
                            tdb2 = document.createElement('td');
                            tdb2.textContent = (status[0] == 1) ? '🟢 active' : '🔴 inactive';
                            trb.appendChild(tdb2);
                            body.appendChild(trb);

                            const cells = document.querySelectorAll('.uid' + CSS.escape(status[1]));
                            //console.log(status[1] + ' cells:', cells);

                            cells.forEach(cell => {
                                cell.textContent = (status[0] == 1) ? '🟢 active' : '🔴 inactive';
                                cell.style.color = (status[0] == 1) ? 'green' : 'red';
                            });
                        });                        
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function closeModal() {
                document.getElementById("productModal").style.display = "none";
                document.getElementById("modalOverlay").style.display = "none";
            }

            checkConnection();

            setInterval(checkConnection, 5000);

        </script>

        <!-- Zatemnené pozadie -->
        <div id="modalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55);"></div>

        <!-- Modal okienko -->
        <div id="productModal" style="display:none; position:fixed; top:25%; left:50%; transform:translate(-50%, -50%); background:white; border:1px solid #999; padding:20px; border-radius: 8px; z-index:1001;">
            <h3>Create New Product</h3>
            <form id="productForm">
                <label>Názov:</label><br>
                <input type="text" name="name" required><br><br>

                <label>Počet:</label><br>
                <input type="number" name="quantity" required><br><br>

                <label>Cena (€):</label><br>
                <input type="number" step="0.01" name="price" required><br><br>

                <button type="submit">Pridať</button>
                <button type="button" id="cls-btn">Zavrieť</button>
            </form>
        </div>

        <script>    
            document.getElementById("cls-btn").onclick = closeModal;
            document.getElementById("modalOverlay").onclick = closeModal;

            document.getElementById('productForm').onsubmit = function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('action', 'add_product');

                fetch('add_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Product added:', data);
                    alert('Produkt bol pridaný.');
                    document.getElementById('productModal').style.display = 'none';
                    //checkConnection();
                })
                .catch(error => console.error('Error adding product:', error));
            };
        </script>
    </body>
</html>