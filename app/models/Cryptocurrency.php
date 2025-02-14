<?php
namespace App\Models;

class Cryptocurrency {
    private $db;
    
    public function __construct() {
        $this->db = new \App\Libraries\Database;
    }

    public function getAll() {
        return $this->getAllCryptos();
    }

    public function findById($id) {
        $this->db->query("SELECT * FROM cryptocurrencies WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function findBySymbol($symbol) {
        $this->db->query("SELECT * FROM cryptocurrencies WHERE symbol = :symbol");
        $this->db->bind(':symbol', $symbol);
        return $this->db->single();
    }

    public function updatePrice($id, $price) {
        $this->db->query("UPDATE cryptocurrencies 
                         SET price = :price, 
                             updated_at = CURRENT_TIMESTAMP 
                         WHERE id = :id");
        
        $this->db->bind(':id', $id);
        $this->db->bind(':price', $price);
        
        return $this->db->execute();
    }

    public function getAllCryptos() {
        $this->db->query("SELECT * FROM cryptocurrencies ORDER BY market_cap DESC");
        return $this->db->resultSet();
    }

    public function getTopCryptos($limit = 10) {
        $this->db->query("SELECT * FROM cryptocurrencies ORDER BY market_cap DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        $result = $this->db->resultSet();
        return array_map(function($crypto) {
            return (array) $crypto;
        }, $result);
    }

    public function updateCryptoData($id, $data) {
        $this->db->query("UPDATE cryptocurrencies SET 
            price = :price,
            market_cap = :market_cap,
            volume_24 = :volume_24,
            circulating_supply = :circulating_supply,
            max_supply = :max_supply
            WHERE id = :id");

        $this->db->bind(':id', $id);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':market_cap', $data['market_cap']);
        $this->db->bind(':volume_24', $data['volume_24']);
        $this->db->bind(':circulating_supply', $data['circulating_supply']);
        $this->db->bind(':max_supply', $data['max_supply']);

        return $this->db->execute();
    }

    public function updateOrInsert($data) {
      
        $this->db->query("SELECT id FROM cryptocurrencies WHERE slug = :slug");
        $this->db->bind(':slug', $data['slug']);
        $crypto = $this->db->single();

        if($crypto) {
          
            $this->db->query("UPDATE cryptocurrencies SET 
                name = :name,
                symbol = :symbol,
                price = :price,
                market_cap = :market_cap,
                volume_24 = :volume_24,
                circulating_supply = :circulating_supply,
                max_supply = :max_supply
                WHERE slug = :slug");
        } else {
            // Insert new crypto
            $this->db->query("INSERT INTO cryptocurrencies 
                (name, symbol, slug, price, market_cap, volume_24, circulating_supply, max_supply) 
                VALUES 
                (:name, :symbol, :slug, :price, :market_cap, :volume_24, :circulating_supply, :max_supply)");
        }

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':symbol', $data['symbol']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':market_cap', $data['market_cap']);
        $this->db->bind(':volume_24', $data['volume_24']);
        $this->db->bind(':circulating_supply', $data['circulating_supply']);
        $this->db->bind(':max_supply', $data['max_supply']);

        return $this->db->execute();
    }
} 