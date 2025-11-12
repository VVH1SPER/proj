CREATE TABLE tosync (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,       -- ID ряда
    product_id INT UNSIGNED,                 -- ID товара
    name VARCHAR(255),                    -- Название товара
    quantity INT UNSIGNED,      -- Количество
    price DECIMAL(10,2),    -- Цена
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    change_type INT NOT NULL,               -- Тип изменения: 1 - вставка, 2 - обновление, 3 - удаление
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;