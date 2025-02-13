<?php
namespace App\models;

class Transaction
{
    private $db;

    public function __construct()
    {
        $this->db = new \App\Libraries\Database;
    }

    public function getUserTransactions($userId, $limit = 10)
    {
        error_log("Getting transactions for user: " . $userId);

        $this->db->query("SELECT COUNT(*) as count FROM transactions WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $count = $this->db->single();
        error_log("Found {$count->count} transactions");

        if ($count->count > 0) {
            $this->db->query("SELECT 
                                t.id,
                                t.amount,
                                t.price,
                                t.status,
                                t.created_at,
                                c.name as crypto_name,
                                c.symbol as crypto_symbol,
                                tt.name as transaction_type 
                            FROM transactions t 
                            JOIN cryptocurrencies c ON t.crypto_id = c.id 
                            JOIN transaction_types tt ON t.type_id = tt.id 
                            WHERE t.user_id = :user_id 
                            ORDER BY t.created_at DESC 
                            LIMIT :limit");

            $this->db->bind(':user_id', $userId);
            $this->db->bind(':limit', $limit);

            $result = $this->db->resultSet();

            return array_map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'price' => $transaction->price,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at,
                    'name' => $transaction->crypto_name,
                    'symbol' => $transaction->crypto_symbol,
                    'type' => $transaction->transaction_type
                ];
            }, $result);
        }
        return [];
    }

    public function addTransaction($data) {
        $this->db->query("INSERT INTO transactions 
                         (user_id, crypto_id, type_id, amount, price, recipient_id) 
                         VALUES 
                         (:user_id, :crypto_id, 
                          (SELECT id FROM transaction_types WHERE name = :type), 
                          :amount, :price, :recipient_id)");

        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':crypto_id', $data['crypto_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':price', $data['price'] ?? null);
        $this->db->bind(':recipient_id', $data['recipient_id'] ?? null);

        return $this->db->execute();
    }
    public function getTransactionTypes() {
        $this->db->query("SELECT * FROM transaction_types");
        return $this->db->resultSet();
    }
    public function createTradeTransaction($userId, $cryptoId, $type, $amount, $price) {
        return $this->createTransaction([
            'user_id' => $userId,
            'crypto_id' => $cryptoId,
            'type' => $type,
            'amount' => $amount,
            'price' => $price
        ]);
    }

}