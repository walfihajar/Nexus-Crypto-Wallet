<?php
namespace App\Controllers;

use App\Libraries\Controller;
use App\Models\Cryptocurrency;
use App\Models\Watchlist;
use App\Services\CryptoService;

class Markets extends Controller {
    private $cryptoModel;
    private $watchlistModel;
    private $cryptoService;

    public function __construct() {
       if(!isLoggedIn()) {
           redirect('auth/login');
       }
        
        $this->cryptoModel = new Cryptocurrency();
        $this->watchlistModel = new Watchlist();
        $this->cryptoService = new CryptoService();
    }

    public function index() {
        try {
            // Get top cryptocurrencies
            $topCryptos = $this->cryptoService->getTopCryptos();

            //die();

            if (!is_array($topCryptos) || empty($topCryptos)) {
                throw new \Exception("Unable to fetch cryptocurrency data");
            }


            
            // inseration des top dans la base de donnee
            foreach($topCryptos as $crypto) {
                echo "<pre>" ;
                var_dump($crypto['slug']);;
                $this->cryptoModel->updateOrInsert($crypto);
                echo "</pre>";
                die();


            }

            // Get user's watchlist and convert to array

            $watchlist = $this->watchlistModel->getFavorites($_SESSION['user_id']);
            $watchlist = $this->watchlistModel->getFavorites($_SESSION['user_id']);

            // Convert watchlist crypto_ids to array for easy comparison
            $watchlistIds = array_map(function($item) {
                return $item['slug'];
            }, $watchlist);

            $data = [
                'topCryptos' => $topCryptos,
                'watchlist' => $watchlist,
                'watchlistIds' => $watchlistIds
            ];

            $this->view('markets/index', $data);
        } catch (\Exception $e) {
            error_log("Markets Error: " . $e->getMessage());
            $data = [
                'error' => 'Unable to fetch cryptocurrency data. Please try again later.',
                'topCryptos' => [],
                'watchlist' => [],
                'watchlistIds' => []
            ];
            $this->view('markets/index', $data);
        }
    }

    public function toggleWatchlist() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $cryptoId = trim($_POST['crypto_id']);
            
            if($this->watchlistModel->isFavorite($_SESSION['user_id'], $cryptoId)) {
                if($this->watchlistModel->removeFavorite($_SESSION['user_id'], $cryptoId)) {
                    echo json_encode(['success' => true, 'action' => 'removed']);
                    return;
                }
            } else {
                if($this->watchlistModel->addFavorite($_SESSION['user_id'], $cryptoId)) {
                    echo json_encode(['success' => true, 'action' => 'added']);
                    return;
                }
            }
            
            echo json_encode(['success' => false]);
        }
    }
} 