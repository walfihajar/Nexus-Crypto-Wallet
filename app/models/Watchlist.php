<?php
namespace App\Models;

class Watchlist {
    private $db;
    
    public function __construct() {
        $this->db = new \App\Libraries\Database;
    }

    public function getFavorites($userId) {
        $this->db->query("SELECT w.*, c.* FROM watchlist w 
                         JOIN cryptocurrencies c ON w.crypto_id = c.id 
                         WHERE w.user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->resultSet();
        return array_map(function($crypto) {
            return (array) $crypto;
        }, $result);
    }

    public function addFavorite($userId, $cryptoSlug) {
        // First get crypto id from slug
        $this->db->query("SELECT id FROM cryptocurrencies WHERE slug = :slug");
        $this->db->bind(':slug', $cryptoSlug);
        $crypto = $this->db->single();
        
        if (!$crypto) return false;

        $this->db->query("INSERT INTO watchlist (user_id, crypto_id) 
                          VALUES (:user_id, :crypto_id)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':crypto_id', $crypto->id);
        return $this->db->execute();
    }

    public function removeFavorite($userId, $cryptoSlug) {
        $this->db->query("DELETE w FROM watchlist w 
                          JOIN cryptocurrencies c ON w.crypto_id = c.id 
                          WHERE w.user_id = :user_id AND c.slug = :slug");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':slug', $cryptoSlug);
        return $this->db->execute();
    }

    public function isFavorite($userId, $cryptoSlug) {
        $this->db->query("SELECT 1 FROM watchlist w 
                          JOIN cryptocurrencies c ON w.crypto_id = c.id 
                          WHERE w.user_id = :user_id AND c.slug = :slug");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':slug', $cryptoSlug);
        return (bool) $this->db->single();
    }
} 