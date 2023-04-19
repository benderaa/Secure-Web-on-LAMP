CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(25) NOT NULL UNIQUE,
  email VARCHAR(25) NOT NULL UNIQUE,
  password_salt VARBINARY(255) NOT NULL,
  password_hash VARBINARY(255) NOT NULL,
  wallet_address VARCHAR(255) NOT NULL UNIQUE,
  wallet_balance DECIMAL(18, 2) NOT NULL DEFAULT 0.00,
  ip_address VARCHAR(15) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE INDEX idx_username ON users (username);
CREATE INDEX idx_email ON users (email);
CREATE INDEX idx_wallet_address ON users (wallet_address);
CREATE INDEX idx_ip_address ONM users (ip_address);
