<?php
namespace App\Models;

class Wallet {
    private $db;
    
    public function __construct() {
        $this->db = new \App\Libraries\Database;
    }

    public function getUserWallets($userId) {
        $this->db->query("SELECT w.*, c.name, c.symbol, c.price 
                         FROM wallets w 
                         JOIN cryptocurrencies c ON w.crypto_id = c.id 
                         WHERE w.user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function deposit($userId, $cryptoId, $amount) {
        $this->db->beginTransaction();
        try {
            // knchofo wach lwallet kayn ola la 
            $this->db->query("SELECT id FROM wallets WHERE user_id = :user_id AND crypto_id = :crypto_id");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':crypto_id', $cryptoId);
            $wallet = $this->db->single();

            if($wallet) {
                // kandiro update lwallet yla kayna
                $this->db->query("UPDATE wallets 
                                 SET balance = balance + :amount 
                                 WHERE user_id = :user_id AND crypto_id = :crypto_id");
            } else {
                // Create new wallet
                $this->db->query("INSERT INTO wallets (user_id, crypto_id, balance) 
                                 VALUES (:user_id, :crypto_id, :amount)");
            }

            $this->db->bind(':user_id', $userId);
            $this->db->bind(':crypto_id', $cryptoId);
            $this->db->bind(':amount', $amount);
            
            if(!$this->db->execute()) {
                throw new \Exception("Failed to update wallet");
            }

            // kansjlo transaction
            $this->db->query("INSERT INTO transactions 
                             (user_id, crypto_id, type_id, amount) 
                             VALUES 
                             (:user_id, :crypto_id, 
                              (SELECT id FROM transaction_types WHERE name = 'DEPOSIT'), 
                              :amount)");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':crypto_id', $cryptoId);
            $this->db->bind(':amount', $amount);

            if(!$this->db->execute()) {
                throw new \Exception("Failed to record transaction");
            }

            $this->db->commit();
            return true;
        } catch(\Exception $e) {
            $this->db->rollBack();
            error_log("Deposit Error: " . $e->getMessage());
            return false;
        }
    }

    public function executeTrade($userId, $cryptoId, $amount, $type, $value) {
        $this->db->beginTransaction();
        try {
            if($type === 'BUY') {
                // kanchekch lbalance USDT
                $this->db->query("SELECT balance FROM wallets w 
                                 JOIN cryptocurrencies c ON w.crypto_id = c.id 
                                 WHERE w.user_id = :user_id AND c.symbol = 'USDT'");
                $this->db->bind(':user_id', $userId);
                $usdt = $this->db->single();

                if(!$usdt || $usdt->balance < $value) {
                    throw new \Exception("Insufficient USDT balance");
                }

                // kn9so lbalance USDT
                $this->db->query("UPDATE wallets w 
                                 SET balance = balance - :value 
                                 WHERE w.user_id = :user_id 
                                 AND w.crypto_id = (SELECT id FROM cryptocurrencies WHERE symbol = 'USDT')");
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':value', $value);
                
                if(!$this->db->execute()) {
                    throw new \Exception("Failed to deduct USDT");
                }

                // kandiro add lbalance crypto
                $this->updateBalance($userId, $cryptoId, $amount);
            } else {
                // knchofo wach lbalance crypto
                $this->db->query("SELECT balance FROM wallets 
                                 WHERE user_id = :user_id AND crypto_id = :crypto_id");
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':crypto_id', $cryptoId);
                $wallet = $this->db->single();

                if(!$wallet || $wallet->balance < $amount) {
                    throw new \Exception("Insufficient crypto balance");
                }

                // kn9so lbalance crypto
                $this->db->query("UPDATE wallets 
                                 SET balance = balance - :amount 
                                 WHERE user_id = :user_id AND crypto_id = :crypto_id");
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':crypto_id', $cryptoId);
                $this->db->bind(':amount', $amount);
                
                if(!$this->db->execute()) {
                    throw new \Exception("Failed to deduct crypto");
                }

                // kandiro add lbalance USDT
                $this->updateBalance($userId, 
                    $this->getUsdtId(), 
                    $value
                );
            }

            // kansjlo transaction
            $this->db->query("INSERT INTO transactions 
                             (user_id, crypto_id, type_id, amount, price) 
                             VALUES 
                             (:user_id, :crypto_id, 
                              (SELECT id FROM transaction_types WHERE name = :type), 
                              :amount, :price)");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':crypto_id', $cryptoId);
            $this->db->bind(':type', $type);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':price', $value/$amount);

            if(!$this->db->execute()) {
                throw new \Exception("Failed to record transaction");
            }

            $this->db->commit();
            return true;
        } catch(\Exception $e) {
            $this->db->rollBack();
            error_log("Trade Error: " . $e->getMessage());
            return false;
        }
    }

    private function updateBalance($userId, $cryptoId, $amount) {
        $this->db->query("INSERT INTO wallets (user_id, crypto_id, balance) 
                         VALUES (:user_id, :crypto_id, :amount) 
                         ON CONFLICT (user_id, crypto_id) 
                         DO UPDATE SET balance = wallets.balance + :amount2");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':crypto_id', $cryptoId);
        $this->db->bind(':amount', $amount);
        $this->db->bind(':amount2', $amount);
        
        return $this->db->execute();
    }

    private function getUsdtId() {
        $this->db->query("SELECT id FROM cryptocurrencies WHERE symbol = 'USDT'");
        $result = $this->db->single();
        return $result ? $result->id : null;
    }

    public function transfer($fromUserId, $toUserId, $cryptoId, $amount) {
        try {
            $this->db->beginTransaction();

            // kanchofo wach li ghaysft 3Ndo solde b3da
            $senderWallet = $this->getUserWallet($fromUserId, $cryptoId);
            if (!$senderWallet || $senderWallet->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            // kandiro get lwallet yla kayna
            $recipientWallet = $this->getUserWallet($toUserId, $cryptoId);
            if (!$recipientWallet) {
                // kancriw lwallet yla mkaynach
                $this->createWallet($toUserId, $cryptoId);
                $recipientWallet = $this->getUserWallet($toUserId, $cryptoId);
            }

            // kn9so mn li sayft
            $this->db->query("UPDATE wallets 
                             SET balance = balance - :amount 
                             WHERE user_id = :user_id 
                             AND crypto_id = :crypto_id");
            $this->db->bind(':amount', $amount);
            $this->db->bind(':user_id', $fromUserId);
            $this->db->bind(':crypto_id', $cryptoId);
            
            if (!$this->db->execute()) {
                throw new \Exception('Failed to update sender wallet');
            }

            // kanzid li ghaysift lih
            $this->db->query("UPDATE wallets 
                             SET balance = balance + :amount 
                             WHERE user_id = :user_id 
                             AND crypto_id = :crypto_id");
            $this->db->bind(':amount', $amount);
            $this->db->bind(':user_id', $toUserId);
            $this->db->bind(':crypto_id', $cryptoId);
            
            if (!$this->db->execute()) {
                throw new \Exception('Failed to update recipient wallet');
            }

            // Rnsjlo transaction
            $this->db->query("INSERT INTO transactions 
                             (user_id, crypto_id, type_id, amount, recipient_id) 
                             VALUES 
                             (:from_user_id, :crypto_id, 
                              (SELECT id FROM transaction_types WHERE name = 'SEND'), 
                              :amount, :to_user_id)");
            
            $this->db->bind(':from_user_id', $fromUserId);
            $this->db->bind(':crypto_id', $cryptoId);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':to_user_id', $toUserId);
            
            if (!$this->db->execute()) {
                throw new \Exception('Failed to record sender transaction');
            }

            // Record recipient's transaction
            $this->db->query("INSERT INTO transactions 
                             (user_id, crypto_id, type_id, amount, recipient_id) 
                             VALUES 
                             (:to_user_id, :crypto_id, 
                              (SELECT id FROM transaction_types WHERE name = 'RECEIVE'), 
                              :amount, :from_user_id)");
            
            $this->db->bind(':to_user_id', $toUserId);
            $this->db->bind(':crypto_id', $cryptoId);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':from_user_id', $fromUserId);
            
            if (!$this->db->execute()) {
                throw new \Exception('Failed to record recipient transaction');
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Transfer Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserWallet($userId, $cryptoId) {
        $this->db->query("SELECT w.*, c.symbol, c.name as crypto_name, c.price 
                         FROM wallets w 
                         JOIN cryptocurrencies c ON w.crypto_id = c.id 
                         WHERE w.user_id = :user_id 
                         AND w.crypto_id = :crypto_id");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':crypto_id', $cryptoId);
        
        return $this->db->single();
    }

    private function createWallet($userId, $cryptoId) {
        $this->db->query("INSERT INTO wallets (user_id, crypto_id, balance) 
                         VALUES (:user_id, :crypto_id, 0)");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':crypto_id', $cryptoId);
        
        return $this->db->execute();
    }
} 