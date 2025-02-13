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

}