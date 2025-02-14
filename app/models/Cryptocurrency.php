<?php

namespace App\Models;

use App\Libraries\Database;
use App\Libraries\DataBaseManager;

class Cryptocurrency
{
    private $dbmanager;
    private $db;

    public function __construct()
    {
        $this->dbmanager = new DataBaseManager;
        $this->db = DataBase::getInstance();

    }

    // public function getAll() {
    //     return $this->getAllCryptos();
    // }

    public function findById($id)
    {
        return $this->dbmanager->selectBy('cryptocurrencies', ['id' => $id]);
    }

    public function findBySymbol($symbol)
    {
        return $this->dbmanager->selectBy('cryptocurrencies', ['symbol' => $symbol]);
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

    public function getAllCryptos()
    {
        $this->dbmanager->getConnection()->query("SELECT * FROM cryptocurrencies ORDER BY market_cap DESC");
        return $this->dbmanager->resultSet();
    }

    public function getTopCryptos($limit = 10)
    {
        $this->dbmanager->query("SELECT * FROM cryptocurrencies ORDER BY market_cap DESC LIMIT :limit");
        $this->d->bind(':limit', $limit);
        $result = $this->db->resultSet();
        return array_map(function ($crypto) {
            return (array)$crypto;
        }, $result);
    }

    // hna dima kandir update l cyrpto
    public function updateCryptoData($id, $data)
    {
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

    public function updateOrInsert($data)
    {
        // Check if crypto kayn 😁
//        $this->dbmanager->getConnection()->query("SELECT id FROM cryptocurrencies WHERE slug = 'bitcoin' ");
//        $this->db->bind(':slug', $data['slug'] ; pdo);
//        $crypto = $this->dbmanager->getConnection()->single();

        $crypto = $this->dbmanager->selectBy('cryptocurrencies', ['slug' => $data['slug']]);
        var_dump($crypto);

        if ($crypto) {
            // Update existing crypto


            $dataUpdate = [
                'name' => $data['name'],
                'symbol' => $data['symbol'], // Correction ici
                'price' => $data['price'],
                'market_cap' => $data['market_cap'],
                'volume_24' => $data['volume_24'],
                'circulating_supply' => $data['circulating_supply'],
                'max_supply' => $data['max_supply']
            ];
            $wheredataUpdate = ['slug' => $data['slug']];


            $this->dbmanager->update('cryptocurrencies', $dataUpdate, $wheredataUpdate);


        } else {
            // Insert new crypto
            $dataInsert = 
                ['name' => $data['name'],
                'symbol' => $data['symbol'],
                'slug' => $data['symbol'],
                 'price' => $data['price'],
                'market_cap' => $data['market_cap'],
                'volume_24' => $data['volume_24'],
                'circulating_supply' => $data['circulating_supply'],
                'max_supply' => $data['max_supply']];

            return   $this->dbmanager->insert('cryptocurrencies', $dataInsert);}


        
    }
}