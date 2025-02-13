<?php
namespace App\controllers;

use App\libraries\Controller;
use App\models\Transaction;
use App\models\Wallet as WalletModel;

class Wallet extends Controller {
    private $walletModel;
    private $transactionModel;
    private $cryptoModel;

    public function __construct() {
        if(!isLoggedIn()) {
            redirect('auth/login');
        }

        $this->walletModel = new WalletModel();
        $this->transactionModel = new Transaction();
        $this->cryptoModel = new Cryptocurrency();
    }

}