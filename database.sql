
CREATE TABLE IF NOT EXISTS users(
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    age TINYINT(3) UNSIGNED NOT NULL,
    country VARCHAR(255) NOT NULL,
    social_media_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY(id),
    UNIQUE KEY(email(191))
);

CREATE TABLE IF NOT EXISTS transactions(
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATETIME NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    user_id BIGINT(20) UNSIGNED NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS receipts(
    receipt_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    original_filename VARCHAR(255) NOT NULL,
    storage_filename VARCHAR(255) NOT NULL,
    media_type VARCHAR(255) NOT NULL,
    transaction_id BIGINT(20) NOT NULL,
    PRIMARY KEY(receipt_id),
    FOREIGN KEY(transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
)