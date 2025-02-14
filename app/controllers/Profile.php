<?php
namespace App\controllers;
use App\Libraries\Controller;
use App\Libraries\User;
use App\Models\Transaction;
use App\Models\Wallet;


class Profile extends Controller
{
    private $userModel;
    private $transactionModel;
    private $walletModel;

    public function __construct()
    {
        if(!isLoggedIn()){
            redirect('auth/login');
        }
        $this->userModel = new User();
        $this->walletModel = new Wallet();
        $this->transactionModel = new Transaction();
    }
    public function index()
    {
        $userId= $_SESSION['user_id']['user_id'];
        $user = $this->userModel->getUserById($userId);

        if(!$user){
            redirect('auth/login');
        }

        $transactions = $this->transactionModel->getTransactionsByUserId($userId,5);
        $wallet = $this->walletModel->getUserWallets($userId);
        $data = [
            'user' => $user,
            'transactions' => $transactions,
            'wallet' => $wallet
        ];
        $this->view('profile', $data);
    }
}

