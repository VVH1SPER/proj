const updatedData = {
                                id: id,
                                name: row.querySelector('.name').textContent.trim(),
                                quantity: row.querySelector('.quant').textContent.trim(),
                                price: row.querySelector('.price').textContent.trim()
                            };

                            fetch('update_row.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: new URLSearchParams(updatedData)
                            })
                            .then(async res => {
                                const text = await res.text();
                                console.log('Raw server response:', text);
                                return JSON.parse(text);
                                //res => res.json();
                            })
                            .then(resp => console.log('Update results:', resp))
                            .catch(err => console.error('Update error:', err));