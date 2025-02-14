<?php
namespace App\Controllers;

use App\Libraries\Controller;
use App\Models\Transaction;
use App\Models\Cryptocurrency;
use App\Models\Wallet as WalletModel;

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

        //  jib transaction mn transaction model
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

            // Get USDT crypto_id
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

    // Handle crypto purchase
    public function buy() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $cryptoId = intval(trim($_POST['crypto_id']));
            $amount = floatval(trim($_POST['amount']));
            
            $crypto = $this->cryptoModel->findById($cryptoId);
            if(!$crypto) {
                flash('trade_error', 'Invalid cryptocurrency');
                redirect('wallet');
                return;
            }

            $cost = $amount * $crypto->price;
            if($this->walletModel->executeTrade($_SESSION['user_id'], $cryptoId, $amount, 'BUY', $cost)) {
                flash('trade_success', 'Purchase successful');
            } else {
                flash('trade_error', 'Insufficient funds or trade failed');
            }

            redirect('wallet');
        }
    }