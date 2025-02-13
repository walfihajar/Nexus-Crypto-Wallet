<?php
namespace App\Models;

use App\Libraries\DataBaseManager;

class Cryptocurrency {
    private $dbmanager;

    public function __construct() {
        $this->dbmanager= new DataBaseManager;
    }

    // public function getAll() {
    //     return $this->getAllCryptos();
    // }

    public function findById($id) {
        return  $this->dbmanager->selectBy('cryptocurrencies' , ['id'=>$id]) ;
    }

    public function findBySymbol($symbol) {
        return  $this->dbmanager->selectBy('cryptocurrencies' , ['symbol'=>$symbol]) ;
    }

//    public function updatePrice($id, $price) {
//        $this->dbmanager->query("UPDATE cryptocurrencies
//                         SET price = :price,
//                             updated_at = CURRENT_TIMESTAMP
//                         WHERE id = :id");
//
//        $this->dbmanager->bind(':id', $id);
//        $this->dbmanager->bind(':price', $price);
//
//        return $this->dbmanager->execute();
//    }

    public function getAllCryptos() {
        $this->dbmanager->getConnection()->query("SELECT * FROM cryptocurrencies ORDER BY market_cap DESC");
        return $this->dbmanager->resultSet();
    }

    public function getTopCryptos($limit = 10) {
        $this->dbmanager->query("SELECT * FROM cryptocurrencies ORDER BY market_cap DESC LIMIT :limit");
        $this->dbmanager->bind(':limit', $limit);
        $result = $this->dbmanager->resultSet();
        return array_map(function($crypto) {
            return (array) $crypto;
        }, $result);
    }

    // hna dima kandir update l cyrpto
    public function updateCryptoData($id, $data) {
        $this->dbmanager->query("UPDATE cryptocurrencies SET 
            price = :price,
            market_cap = :market_cap,
            volume_24 = :volume_24,
            circulating_supply = :circulating_supply,
            max_supply = :max_supply
            WHERE id = :id");

        $this->dbmanager->bind(':id', $id);
        $this->dbmanager->bind(':price', $data['price']);
        $this->dbmanager->bind(':market_cap', $data['market_cap']);
        $this->dbmanager->bind(':volume_24', $data['volume_24']);
        $this->dbmanager->bind(':circulating_supply', $data['circulating_supply']);
        $this->dbmanager->bind(':max_supply', $data['max_supply']);

        return $this->dbmanager->execute();
    }

    public function updateOrInsert($data) {
        // Check if crypto kayn 😁
        $this->dbmanager->getConnection()->query("SELECT id FROM cryptocurrencies WHERE slug = :slug");
        $this->dbmanager->bind(':slug', $data['slug']);
        $crypto = $this->dbmanager->single();

        if($crypto) {
            // Update existing crypto
            $this->dbmanager->query("UPDATE cryptocurrencies SET 
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
            $this->dbmanager->query("INSERT INTO cryptocurrencies 
                (name, symbol, slug, price, market_cap, volume_24, circulating_supply, max_supply) 
                VALUES 
                (:name, :symbol, :slug, :price, :market_cap, :volume_24, :circulating_supply, :max_supply)");
        }

        $this->dbmanager->bind(':name', $data['name']);
        $this->dbmanager->bind(':symbol', $data['symbol']);
        $this->dbmanager->bind(':slug', $data['slug']);
        $this->dbmanager->bind(':price', $data['price']);
        $this->dbmanager->bind(':market_cap', $data['market_cap']);
        $this->dbmanager->bind(':volume_24', $data['volume_24']);
        $this->dbmanager->bind(':circulating_supply', $data['circulating_supply']);
        $this->dbmanager->bind(':max_supply', $data['max_supply']);

        return $this->dbmanager->execute();
    }
}