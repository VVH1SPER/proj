CREATE TABLE products (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,       -- ID ряда
    name VARCHAR(255) NOT NULL,                    -- Название товара
    quantity INT UNSIGNED NOT NULL DEFAULT 0,      -- Количество
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,    -- Цена
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,            -- Дата создания
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Дата последнего изменения
    user_id INT UNSIGNED NOT NULL,                 -- ID пользователя, которому разрешено изменять
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

