<?php
namespace App\Services;

class CryptoService {
    private $baseUrl = 'https://api.coingecko.com/api/v3';

    public function getTopCryptos($limit = 10) {
        $url = $this->baseUrl . "/coins/markets?" . http_build_query([
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => $limit,
                'page' => 1,
                'sparkline' => false,
                'locale' => 'en'
            ]);

        $response = $this->makeRequest($url);
        if (!$response) return [];

        return array_map(fn($crypto) => [
            'id' => $crypto['id'],
            'name' => $crypto['name'],
            'symbol' => strtoupper($crypto['symbol']),
            'slug' => $crypto['id'],
            'price' => $crypto['current_price'],
            'market_cap' => $crypto['market_cap'],
            'volume_24' => $crypto['total_volume'],
            'circulating_supply' => $crypto['circulating_supply'],
            'max_supply' => $crypto['max_supply'] ?? null
        ], $response);
    }

    public function getCryptoBySlug($slug) {
        $url = "{$this->baseUrl}/coins/{$slug}?localization=false&tickers=false&market_data=true";
        return $this->makeRequest($url);
    }

    private function makeRequest($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ["Accept: application/json", "User-Agent: MyCryptoApp"]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 200) ? json_decode($response, true) : false;
    }
}
