CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(254) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user',  'admin') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    action ENUM('login', 'logout', 'registration', 'view_page', 'button_click') NOT NULL,
    performed_on ENUM('page_a', 'page_b', 'btn_buy_cow', 'btn_download') NULL,
    ip_address VARBINARY(16) NULL,
    created_at DATETIME(3) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_created_at (created_at),
    KEY idx_user_id_created_at (user_id, created_at),
    KEY idx_action_created_at (action, created_at),
    KEY idx_performed_on_created_at (performed_on, created_at),
    CONSTRAINT fk_events_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT chk_events_performed_on CHECK (
        (
            action = 'view_page'
            AND performed_on IS NOT NULL
            AND performed_on IN ('page_a', 'page_b')
        )
        OR (
            action = 'button_click'
            AND performed_on IS NOT NULL
            AND performed_on IN ('btn_buy_cow', 'btn_download')
        )
        OR (
            action IN ('login', 'logout', 'registration')
            AND performed_on IS NULL
        )
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;