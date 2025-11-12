CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,       -- ID ряда
    login VARCHAR(255) NOT NULL,                    -- Название товара
    pass VARCHAR(255) NOT NULL,
    db VARCHAR(255) NOT NULL,
    ip VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (login, pass, db, ip)
VALUES
('root', '', 'dtbaza', 'localhost')
('uid2', '12345', 'dtbaza', '25.30.27.137')