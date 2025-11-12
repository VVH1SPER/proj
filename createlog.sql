CREATE TABLE log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    server_name VARCHAR(64),
    query_text TEXT,
    reason VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);