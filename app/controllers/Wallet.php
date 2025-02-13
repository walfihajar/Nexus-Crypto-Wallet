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

    public function deposit() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $amount = floatval(trim($_POST['amount']));
            
            if($amount <= 0) {
                flash('deposit_error', 'Invalid amount');
                redirect('wallet');
                return;
            }

            $usdt = $this->cryptoModel->findBySymbol('USDT');
            if(!$usdt) {
                flash('deposit_error', 'System error');
                redirect('wallet');
                return;
            }

            if($this->walletModel->deposit($_SESSION['user_id'], $usdt->id, $amount)) {
                flash('deposit_success', 'Deposit successful');
            } else {
                flash('deposit_error', 'Deposit failed');
            }

            redirect('wallet');
        }
    }

}