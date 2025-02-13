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
    public function index() {
        if(!isLoggedIn()) {
            redirect('auth/login');
        }

        $userId = $_SESSION['user_id'];
        error_log("User ID in session: " . $userId);

        $transactions = $this->transactionModel->getUserTransactions($userId, 10);
        error_log("Transactions: " . print_r($transactions, true));

        $data = [
            'wallets' => $this->walletModel->getUserWallets($userId),
            'transactions' => $transactions,
            'cryptocurrencies' => $this->cryptoModel->getAllCryptos()
        ];

        $this->view('wallet/index', $data);
    }

}