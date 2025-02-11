CREATE TABLE users (
                       id SERIAL PRIMARY KEY,
                       nexus_id VARCHAR(50) UNIQUE,
                       firstname VARCHAR(100),
                       lastname VARCHAR(100),
                       email VARCHAR(100) UNIQUE,
                       password VARCHAR(255),
                       birth_date DATE,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       verified BOOLEAN DEFAULT FALSE,
                       balance NUMERIC(20,2) DEFAULT 0.00
);

CREATE TABLE cryptocurrencies (
                                  id SERIAL PRIMARY KEY,
                                  name VARCHAR(100),
                                  symbol VARCHAR(20),
                                  slug VARCHAR(100),
                                  current_price NUMERIC(20,8),
                                  market_cap NUMERIC(20,2),
                                  volume_24h NUMERIC(20,2),
                                  circulating_supply NUMERIC(20,2),
                                  total_supply NUMERIC(20,2),
                                  max_supply NUMERIC(20,2),
                                  last_updated TIMESTAMP,
                                  price_change_24h NUMERIC(10,2) DEFAULT 0
);

CREATE TABLE watchlists (
                            id SERIAL PRIMARY KEY,
                            user_id INT,
                            crypto_id INT,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (user_id) REFERENCES users(id),
                            FOREIGN KEY (crypto_id) REFERENCES cryptocurrencies(id)
);

CREATE TABLE portfolios (
                            id SERIAL PRIMARY KEY,
                            user_id INT,
                            crypto_id INT,
                            amount NUMERIC(20,8),
                            FOREIGN KEY (user_id) REFERENCES users(id),
                            FOREIGN KEY (crypto_id) REFERENCES cryptocurrencies(id)
);

CREATE TABLE transactions (
                              id SERIAL PRIMARY KEY,
                              user_id INT,
                              crypto_id INT,
                              type VARCHAR(10) CHECK (type IN ('BUY', 'SELL', 'SEND', 'RECEIVE')),
                              amount NUMERIC(20,8),
                              price_at_transaction NUMERIC(20,8),
                              recipient_id INT NULL,
                              transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              FOREIGN KEY (user_id) REFERENCES users(id),
                              FOREIGN KEY (crypto_id) REFERENCES cryptocurrencies(id),
                              FOREIGN KEY (recipient_id) REFERENCES users(id)
);

CREATE TABLE notifications (
                               id SERIAL PRIMARY KEY,
                               user_id INT,
                               message TEXT,
                               type VARCHAR(50),
                               is_read BOOLEAN DEFAULT FALSE,
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                               FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE verification_codes (
                                    id SERIAL PRIMARY KEY,
                                    user_id INT,
                                    code VARCHAR(6),
                                    type VARCHAR(50),
                                    expires_at TIMESTAMP,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (user_id) REFERENCES users(id)
);

